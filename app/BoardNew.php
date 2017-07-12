<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Carbon\Carbon;
use DB;
use Cache;
use App\Write;
use App\Comment;
use App\Point;
use App\BoardFile;

class BoardNew extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public $timestamps = false;

    public function getIndexParams($request)
    {
        $groups = Group::orderBy('group_id')->get();
        $query = $this->getNewWritesThroughSearch($request, $groups);
        $pageRows = Cache::get('config.homepage')->pageRows;
        $boardNewList = $query->paginate($pageRows);
        $boardNewList = $this->processBoardNewList($boardNewList);

        return [
            'groups' => $groups,
            'boardNewList' => $boardNewList,
            'groupId' => isset($request->groupId) ? $request->groupId : '',
            'type' => isset($request->type) ? $request->type : '',
            'nick' => isset($request->nick) ? $request->nick : '',
            'today' => Carbon::now(),
        ];
    }

    // 새글 목록에 검색 조건 추가
    private function getNewWritesThroughSearch($request, $groups)
    {
        $query =
            BoardNew::selectRaw('board_news.*, boards.table_name, boards.subject, boards.mobile_subject, groups.subject as group_subject, groups.id as group_id')
            ->leftJoin('boards', 'boards.id', '=', 'board_news.board_id')
            ->leftJoin('groups', 'groups.id', '=', 'boards.group_id')
            ->where('boards.use_search', 1);

        $groupId = isset($request->groupId) ? $request->groupId : '';
        $type = isset($request->type) ? $request->type : '';
        $nick = isset($request->nick) ? $request->nick : '';

        if($groupId) {
            $query = $query->where('groups.id', $groupId);
        }
        if($type) {
            if($type == 'w') {
                $query = $query->whereColumn('board_news.write_id', '=', 'board_news.write_parent');
            } else {
                $query = $query->whereColumn('board_news.write_id', '<>', 'board_news.write_parent');
            }
        }
        if($nick) {
            $user = User::where('nick', $nick)->first();
            $userId = is_null($user) ? -1 : $user->id;  // 검색한 닉네임이 존재하지 않으면 user->id 를 -1로 검색
            $query = $query->where('board_news.user_id', $userId);
        }

        $query = $query->orderBy('board_news.id', 'desc');

        return $query;
    }

    // 새글 목록에 화면 표시용 데이터 추가
    public function processBoardNewList($boardNewList)
    {
        foreach($boardNewList as $boardNew) {
            $writeTable = 'write_'.$boardNew->table_name;
            $write = DB::table($writeTable)->where('id', $boardNew->write_parent)->first(); // 원글
            $user = $boardNew->user_id ? User::find($boardNew->user_id) : new User();
            // 원글, 댓글 공통 추가 데이터
            $boardNew->write = $write;
            $boardNew->user_email = $user->email;
            $boardNew->user_id_hashkey = $user->id_hashkey;
            $boardNew->commentTag = '';
            $boardNew->name = $write->name;
            // 댓글은 데이터 따로 추가
            if($boardNew->write_id != $boardNew->write_parent) {
                $comment = DB::table($writeTable)->where('id', $boardNew->write_id)->first(); // 댓글
                $boardNew->write->subject = '[코] '. $write->subject;    // [코] + 원글의 제목
                $boardNew->commentTag = '#comment'.$comment->id;
                $boardNew->write->created_at = $comment->created_at;
                $boardNew->name = $comment->name;
            }
        }

        return $boardNewList;
    }

    // 새글 선택 삭제
    public function deleteWrites($ids)
    {
        $boardNews = BoardNew::selectRaw('board_news.*, boards.table_name')
                    ->leftJoin('boards', 'boards.id', '=', 'board_news.board_id')
                    ->whereIn('board_news.id', $ids)
                    ->get();
        $comment = new Comment();
        $point = new Point();
        $boardFile = new BoardFile();
        $message = '';
        foreach($boardNews as $boardNew) {
            $write = new Write($boardNew->board_id);
            $write->setTableName($boardNew->table_name);
            $boardId = $boardNew->board_id;
            $writeId = $boardNew->write_id;
            // 원글 삭제
            if($writeId == $boardNew->write_parent) {
                $delPointResult = $point->deleteWritePoint($write, $boardId, $writeId);
                if($delPointResult <= 0) {
                    $message .= '정상적으로 게시글을 삭제하는데 실패하였습니다.(포인트 삭제)';
                }
                // 서버에서 파일 삭제, 썸네일 삭제, 에디터 첨부 이미지 파일, 썸네일 삭제, 파일 테이블 삭제
                $delFileResult = $boardFile->deleteWriteAndAttachFile($boardId, $writeId);
                if( array_search(false, $delFileResult) === false ) {
                    $message .= '정상적으로 게시글을 삭제하는데 실패하였습니다.(첨부 파일 삭제)';
                }
                // 게시글 삭제
                $delWriteResult = $write->deleteWrite($write, $writeId);
                if($delWriteResult <= 0) {
                    $message .= '정상적으로 게시글을 삭제하는데 실패하였습니다.(글 삭제)';
                }
            } else {    // 댓글 삭제
                $message = $comment->deleteComment($write, $boardId, $writeId);
            }
        }

        return $message;
    }

}
