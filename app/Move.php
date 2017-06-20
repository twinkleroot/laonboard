<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;
use File;
use App\BoardFile;
use Carbon\Carbon;
use App\Board;
use App\Common\Util;

class Move
{
    // (게시판) 관리자의 선택 복사, 이동에 필요한 파라미터
    public function getMoveParams($boardId, $request)
    {
        // 세션에 해당 게시물 아이디들을 보관
        $moveId = $request->has('chkId') ? $request->chkId : $request->writeId;
        session()->put('writeIds', $moveId);

        return [
            'boards' => Board::orderBy('group_id', 'desc')->orderBy('subject', 'desc')->get(),
            'currentBoard' => Board::find($boardId),
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

        $message = '';
        if( !is_null($boards) ) {
            foreach($boards as $board) {
                // 게시판 테이블 셋팅
                // $destinationWrite : 복사되서 게시물이 추가되는 게시판
                $destinationWrite = new Write($board->table_name);
                $destinationWrite->setTableName($board->table_name);
                // num의 최소값
                $minNum = is_null($destinationWrite->min('num')) ? 0 : $destinationWrite->min('num');

                // $originalWrites : 복사할 원본 글들
                // 댓글도 함께 복사 처리가 추가 되야 함
                // 컬렉션의 반복문
                $beforeWriteNum = 0;
                $parent = 0;
                foreach($originalWrites as $originalWrite) {
                    $insertArray = array_except($originalWrite->toArray(), ['id', 'isReply', 'isEdit', 'isDelete']);
                    $destinationWrite->insert($insertArray);  // 새로 insert하기 때문에 auto increment 되는 id값은 제거
                    // 복사할 글을 복사한 테이블에 맞춰서 parent 재설정
                    $lastInsertId = DB::getPdo()->lastInsertId();   // 마지막에 삽입한 행의 id 값 가져오기
                    $newWrite = $destinationWrite->find($lastInsertId);
                    if(!$originalWrite->is_comment && !$originalWrite->reply) {
                        $parent = $lastInsertId;
                    }

                    $toUpdateColumn = [
                        'num' => $minNum,
                        'parent' => $parent,
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
                Util::deleteCache('main', $board->table_name);
            }
            $message = '게시물 복사가 완료되었습니다.';
        } else {
            $message = '게시물 복사에 실패하였습니다.';
        }

        return $message;
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
            $numArray = array_unique($numArray);
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
        $boardId = $request->boardId;
        $writeId = $write->id;
        $boardFiles = BoardFile::where(['board_id' => $boardId, 'write_id' => $writeId])->get();

        $board = Board::find($boardId);

        foreach($boardFiles as $boardFile) {
            $copyBoardFile = $boardFile->toArray();
            $copyBoardFile['write_id'] = $lastInsertId;
            $copyBoardFile['board_id'] = $toBoard->id;
            $copyBoardFile['board_file_no'] = $boardFile->board_file_no;

            BoardFile::insert($copyBoardFile);
            // 파일복사(다른 테이블로 복사했을 경우)
            if($boardId != $toBoard->id) {
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
        if(gettype($writeIds) == 'string') {
            $writes = $writeModel->where('id', $writeIds)->get();
        } else {
            $writes = $writeModel->whereIn('id', $writeIds)->get();
        }

        $message = '';
        foreach($writes as $write) {
            $message .= $this->deleteMovedFileAndWrite($writeModel, $request->boardId, $write->id, $request->type);
        }

        return $message;
    }

    // 기존 원본 첨부파일 삭제
    private function deleteMovedFileAndWrite($writeModel, $boardId, $writeId, $type)
    {
        $message = '';
        // 서버에서 파일 삭제, 썸네일 삭제, 에디터 첨부 이미지 파일, 썸네일 삭제, 파일 테이블 삭제
        $boardFile = new BoardFile();
        $delFileResult = $boardFile->deleteWriteAndAttachFile($boardId, $writeId, $type);
        if( array_search(false, $delFileResult) === false ) {
            $message .= '정상적으로 게시글을 이동하는데 실패하였습니다.('. $boardId. '게시판'. $writeId. '번 글의 첨부 파일 삭제)\\n';
        }

        // 게시글 삭제
        $delWriteResult = $writeModel->deleteWrite($writeModel, $writeId);
        if($delWriteResult <= 0) {
            $message .= '정상적으로 게시글을 이동하는데 실패하였습니다.('. $boardId. '게시판'. $writeId. '번 글의 삭제)\\n';
        }

        return $message;
    }

}
