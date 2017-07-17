<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Cache;
use Exception;
use Carbon\Carbon;
use App\Common\Util;
use App\Comment;
use App\Notification;

class Scrap extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public $timestamps = false;

    // 스크랩 목록 가져오기
    public function getIndexParams()
    {
        $user = auth()->user();
        $scraps = DB::table("scraps as s")
            ->select(DB::raw("
            s.*,
            (   select subject
                from boards as b
                where b.id = s.board_id
            ) as board_subject
            "))
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
                    $writeModel = new Write($scrap->board_id);
                    $writeModel->setTableName($writeModel->board->table_name);
                    session()->put('write_table_num_'. $scrap->board_id, $writeModel);
                }

                $writeModel = session()->get('write_table_num_'. $scrap->board_id);
                $write = $writeModel->find($scrap->write_id);
                if(is_null($write)) {
                    $scrap->write_empty = 1;
                    $scrap->write_subject = '';
                } else {
                    $scrap->write_subject = Util::getText(Util::cutString($write->subject, 20));
                }
            }
        }

        return $scraps;
    }

    // 스크랩 했는지 조회
    public function getScrap($request)
    {
        $scrap = Scrap::where([
            'user_id' => auth()->user()->id,
            'board_id' => $request->boardId,
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
        $writeModel = new Write($request->boardId);
        $writeModel->setTableName($writeModel->board->table_name);

        return $writeModel;
    }

    // 스크랩 저장
    public function storeScrap($request)
    {
        $writeModel = $this->getWriteModel($request);
        $write = $writeModel->find($request->writeId);
        if( !$write ) {
            return [ 'message' => '스크랩하시려는 게시글이 존재하지 않습니다.' ];
        }
        $existScrap = $this->getScrap($request);
        if($existScrap) {
             return [ 'confirm' => '이미 스크랩하신 글 입니다.\\n\\n지금 스크랩을 확인하시겠습니까?' ];
        }


        $comment = new Comment();
        $notification = new Notification();
        $result;
        try {
            $result = $comment->storeComment($writeModel, $request);
        } catch (Exception $e) {
            return [ 'message' => $e->getMessage() ];
        }

        return Scrap::Create([
            'user_id' => auth()->user()->id,
            'board_id' => $request->boardId,
            'write_id' => $request->writeId,
            'created_at' => Carbon::now()
        ]);
    }
}
