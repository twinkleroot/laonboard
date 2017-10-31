<?php

namespace App\Models;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use File;

class BoardMove
{
    // (게시판) 관리자의 선택 복사, 이동에 필요한 파라미터
    public function getMoveParams($boardName, $request)
    {
        // 세션에 해당 게시물 아이디들을 보관
        $moveId = $request->filled('chkId') ? $request->chkId : $request->writeId;
        session()->put('move_writeIds', $moveId);

        return [
            'boards' => Board::orderBy('group_id', 'desc')->orderBy('subject', 'desc')->get(),
            'currentBoard' => Board::getBoard($boardName, 'table_name'),
            'type' => $request->type,
        ];
    }
    // (게시판) 게시물 복사, 게시물 이동 = 복사 + 기존 테이블에서 삭제
    // $writeModel : 원본 게시물 데이터 모델
    // $writeIds : 복사할 대상 게시물들의 id
    // $boardIds : 선택한 대상 게시판들의 id
    public function copyWrites($writeModel, $writeIds, $request)
    {
        $boardIds = $request->chkId;

        $writeNums = $this->getWriteNums($writeModel, $writeIds);
        // 복사할 대상 게시물들
        if(gettype($writeIds) == 'string') {
            $originalWrites = $writeModel->where('num', $writeNums)->get();
        } else {
            $originalWrites = $writeModel->whereIn('num', $writeNums)->get();
        }
        // 선택한 대상 게시판들
        $boards = Board::whereIn('id', $boardIds)->get();

        if($boards) {
            foreach($boards as $board) {
                // 게시판 테이블 셋팅
                // $destinationWrite : 복사되서 게시물이 추가되는 게시판
                $destinationWrite = new Write();
                $destinationWrite->setTableName($board->table_name);
                // num의 최소값
                $minNum = is_null($destinationWrite->min('num')) ? 0 : $destinationWrite->min('num');

                // $originalWrites : 복사할 원본 글들
                // 댓글도 함께 복사 처리가 추가 되야 함
                // 컬렉션의 반복문
                $beforeWriteNum = 0;
                $parent = 0;

                foreach($originalWrites as $originalWrite) {
                    // 새로 insert하기 때문에 auto increment 되는 id값은 제거
                    $insertArray = array_except($originalWrite->toArray(), ['id', 'isReply', 'isEdit', 'isDelete']);
                    $lastInsertId = $destinationWrite->insertGetId($insertArray);
                    // 복사할 글을 복사한 테이블에 맞춰서 parent 재설정
                    $newWrite = Write::getWrite($board->id, $lastInsertId);
                    if(!$originalWrite->is_comment && !$originalWrite->reply) {
                        $parent = $lastInsertId;
                    }

                    $content = $originalWrite->content;
                    // 설정에 복사,이동시 로그 남김 체크한 경우 로그 남기는 기능
                    if(!$originalWrite->is_comment && cache('config.homepage')->useCopyLog) {
                        if(str_contains($originalWrite->option, 'html')) {
                            $logTag1 = '<div class="content_'.$request->type.'">';
                            $logTag2 = '</div>';
                        } else {
                            $logTag1 = "\n";
                            $logTag2 = '';
                        }

                        $content .= $logTag1. '[이 게시물은 '. auth()->user()->nick. '님에 의해 '. Carbon::now(). ' '. $writeModel->board->subject. '게시판에서 '. ($request->type == 'copy' ? '복사' : '이동'). ' 됨]'. $logTag2;
                    }

                    $toUpdateColumn = [
                        'num' => $minNum,
                        'parent' => $parent,
                        'content' => $content,
                    ];

                    // 복사할 글을 복사한 테이블에 맞춰서 num 재설정
                    if($beforeWriteNum == $newWrite->num) {
                        $toUpdateColumn['num'] = $minNum;
                    } else {
                        $toUpdateColumn['num'] = --$minNum;
                    }

                    $destinationWrite->where('id', $lastInsertId)->update($toUpdateColumn);

                    // 복사할 때 원본 게시물에 첨부 파일이 있다면 board_files 테이블에 동일한 파일을 링크하는 정보를 추가해준다.
                    // 게시물 이동할 때는 board_files 테이블의 board_id와 write_id를 update(실제로는 row의 insert -> delete)한다.
                    if($originalWrite->file > 0) {
                        $this->updateAttachedFileInfo($originalWrite, $lastInsertId, $board, $request);
                    }
                    $beforeWriteNum = $newWrite->num;
                }

                // 메인 최신글 캐시 삭제
                deleteCache('main', $board->table_name);
            }
        } else {
            abort(500, '게시물 복사에 실패하였습니다.');
        }
    }

    // 선택한 id들을 갖고 num값을 얻는다.
    private function getWriteNums($writeModel, $writeIds)
    {
        if(gettype($writeIds) == 'string') {
            $write = $writeModel->find($writeIds);
            return $write->num;
        } else {
            $numArray = [];
            foreach($writeIds as $writeId) {
                $write = $writeModel->find($writeId);
                $numArray = array_add($numArray, $write->id, $write->num);
            }
            $numArray = array_values(array_unique($numArray));
            $result = [];
            foreach($numArray as $key => $value) {
                array_push($result, $value);
            }
            return $result;
        }
    }

    // 게시물 복사할 때 첨부파일정보도 함께 복사하는 메서드
    private function updateAttachedFileInfo($write, $lastInsertId, $toBoard, $request)
    {
        $writeId = $write->id;
        $board = Board::getBoard($request->boardName, 'table_name');
        $boardFiles = BoardFile::where(['board_id' => $board->id, 'write_id' => $writeId])->get();

        foreach($boardFiles as $boardFile) {
            $copyBoardFile = $boardFile->toArray();
            $copyBoardFile['write_id'] = $lastInsertId;
            $copyBoardFile['board_id'] = $toBoard->id;
            $copyBoardFile['board_file_no'] = $boardFile->board_file_no;

            BoardFile::insert($copyBoardFile);
            // 파일복사(다른 테이블로 복사했을 경우)
            if($board->id != $toBoard->id) {
                $this->copyFile($boardFile, $board, $toBoard);
            }
        }
    }

    // 파일 시스템에서 파일 복사
    private function copyFile($boardFile, $board, $toBoard)
    {
        $from = storage_path('app/public/'. $board->table_name. '/'. $boardFile->file);
        $toDirectory = storage_path('app/public/'. Board::find($toBoard->id)->table_name);
        $to = $toDirectory. '/'. $boardFile->file;
        if( !File::exists($toDirectory) ) {
            File::makeDirectory($toDirectory);
        }
        File::copy($from, $to);
    }

    // 복사 후 이동
    public function moveWrites($writeModel, $writeIds, $request)
    {
        // 복사할 대상 게시물들
        $writes;
        if(is_string($writeIds)) {
            $writes = $writeModel->where('id', $writeIds)->get();
        } else {
            $writes = $writeModel->whereIn('id', $writeIds)->get();
        }

        foreach($writes as $write) {
            $this->deleteMovedFileAndWrite($writeModel, $request->boardName, $write->id, $request->type);
        }
    }

    // 기존 원본 첨부파일 삭제
    private function deleteMovedFileAndWrite($writeModel, $boardName, $writeId, $type)
    {
        // 서버에서 첨부파일+첨부파일의 썸네일 삭제, 파일 테이블 삭제
        $boardFile = new BoardFile();
        $board = Board::getBoard($boardName, 'table_name');
        $result = $boardFile->deleteWriteAndAttachFile($board->id, $writeId, $type);
        if(!$result) {
            abort(500, '정상적으로 게시글을 이동하는데 실패하였습니다.\\n('. $boardName. '게시판 '. $writeId. '번 글의 첨부 파일 삭제)');
        }

        // 게시글 삭제
        $result = $writeModel->deleteWrite($writeModel, $writeId);
        if($result <= 0) {
            abort(500, '정상적으로 게시글을 이동하는데 실패하였습니다.\\n('. $boardName. '게시판 '. $writeId. '번 글의 삭제)');
        }
    }

}
