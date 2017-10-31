<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Cache;
use Exception;
use Carbon\Carbon;

class Scrap extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public $timestamps = false;

    public function __construct()
    {
        $this->table = 'scraps';
    }

    // 스크랩 목록 가져오기
    public function getIndexParams()
    {
        $user = auth()->user();
        $scraps = Scrap::select('scraps.*', 'boards.subject as board_subject', 'boards.table_name as table_name')
            ->leftJoin('boards', 'boards.id', '=', 'scraps.board_id')
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->paginate(Cache::get('config.homepage')->pageRows);

        // 조회한 Scrap 컬렉션에 게시글 제목 추가
        $scraps = $this->processScraps($scraps);

        return [
            'user' => $user,
            'scraps' => $scraps
        ];
    }

    // $scraps 컬렉션에 게시판 제목, 게시글 제목 넣기
    private function processScraps($scraps)
    {
        foreach($scraps as $scrap) {
            if(is_null($scrap->board_subject)) {
                $scrap->board_empty = 1;
                $scrap->board_subject = '[게시판 없음]';
                $scrap->write_empty = 1;
                $scrap->write_subject = '';
            } else {
                // 각 게시판은 테이블명이 각각 다르므로 테이블명을 셋팅해줘야 한다.
                if( !session()->get('write_table_num_'. $scrap->board_id)) {
                    $writeModel = new Write();
                    $writeModel->board = Board::getBoard($scrap->board_id);
                    $writeModel->setTableName($writeModel->board->table_name);
                    session()->put('write_table_num_'. $scrap->board_id, $writeModel);
                }

                $writeModel = session()->get('write_table_num_'. $scrap->board_id);
                $write = $writeModel->find($scrap->write_id);
                if(is_null($write)) {
                    $scrap->write_empty = 1;
                    $scrap->write_subject = '';
                } else {
                    $scrap->write_subject = convertText(cutString($write->subject, 20));
                }
            }
        }

        return $scraps;
    }

    // 스크랩 했는지 조회
    public function getScrap($request)
    {
        $userId = auth()->user() ? auth()->user()->id : 0;
        $boardId = Board::getBoard($request->boardName, 'table_name')->id;

        $scrap = Scrap::where([
            'user_id' => $userId,
            'board_id' => $boardId,
            'write_id' => $request->writeId,
        ])->first();

        return $scrap;
    }

    public function getWrite($request)
    {
        $write = $this->getWriteModel($request)->find($request->writeId);

        return $write;
    }

    private function getWriteModel($request)
    {
        $writeModel = app()->tagged('write')[0];
        $writeModel->board = Board::getBoard($request->boardName, 'table_name');
        $writeModel->setTableName($writeModel->board->table_name);

        return $writeModel;
    }

    // 스크랩 저장
    public function storeScrap($request)
    {
        $writeModel = $this->getWriteModel($request);
        $write = $writeModel->find($request->writeId);
        if( !$write ) {
            throw new Exception('스크랩하시려는 게시글이 존재하지 않습니다.');
        }
        $existScrap = $this->getScrap($request);
        if($existScrap) {
             return 'exist';
        }

        if(mb_strlen($request->content, 'utf-8') > 0) {
            $comment = new Comment();
            $comment->storeComment($writeModel, $request);
        }

        return Scrap::insert([
            'user_id' => auth()->user()->id,
            'board_id' => $request->boardId,
            'write_id' => $request->writeId,
            'created_at' => Carbon::now()
        ]);
    }
}
