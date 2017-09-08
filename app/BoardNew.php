<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;
use Cache;
use File;

class BoardNew extends Model
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
        $this->table = 'board_news';
    }

    public function deleteOldWrites()
    {
        $newDel = cache('config.homepage')->newDel;

        BoardNew::
            where('created_at', '<', Carbon::now()->subDays($newDel)->toDatetimeString())
            ->delete();
    }

    public function getIndexParams($request)
    {
        $groups = Group::orderBy('group_id')->get();
        $query = $this->getNewWritesThroughSearch($request);
        $pageRows = cache('config.homepage')->pageRows;
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
    private function getNewWritesThroughSearch($request)
    {
        $query =
            BoardNew::select('board_news.*', 'boards.table_name', 'boards.subject', 'groups.subject as group_subject', 'groups.id as group_id')
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
        $writeModel = new Write();
        $boardList = $this->createBoardList($boardNewList);
        $userList = $this->createUserList($boardNewList);
        $writeList = [];
        foreach($boardNewList as $boardNew) {
            $board = $boardList[$boardNew->board_id];
            // 한 페이지에서 원글은 한번만 호출 하도록 한다.
            $writeModel->setTableName($board->table_name);
            if( !array_has($writeList, $boardNew->write_parent) ) {
                $writeList = array_add($writeList, $boardNew->write_parent, $writeModel->find($boardNew->write_parent));
            }
            $write = $writeList[$boardNew->write_parent];
            $user = $userList[$boardNew->user_id];
            // 회원 아이콘 경로 추가
            if($write->user_id && cache('config.join')->useMemberIcon) {
                $iconPath = storage_path('app/public/user'). '/'. mb_substr($write->email, 0, 2, 'utf-8'). '/'. $write->email. '.gif';
                if(File::exists($iconPath)) {
                    $write->iconPath = '/storage/user/'. mb_substr($write->email, 0, 2, 'utf-8'). '/'. $write->email. '.gif';
                }
            }
            // 원글, 댓글 공통 추가 데이터
            $boardNew->write = $write;
            $subject = subjectLength($write->subject, 60);
            $boardNew->write->subject = $subject;
            $boardNew->user_email = $user->email;
            $boardNew->user_id_hashkey = $user->id_hashkey;
            $boardNew->commentTag = '';
            $boardNew->name = $write->name;

            // 댓글은 데이터 따로 추가
            if($boardNew->write_id != $boardNew->write_parent) {
                $comment = $writeModel->find($boardNew->write_id);	 // 댓글
                $boardNew->write->subject = '[코] '. $subject;    // [코] + 원글의 제목
                $boardNew->commentTag = '#comment'.$comment->id;
                $boardNew->write->created_at = $comment->created_at;
                $boardNew->name = $comment->name;
            }
        }

        return $boardNewList;
    }

    // 한 페이지에서 한 게시판 및 그룹은 한번만 불러오도록 게시판 리스트를 만들어서 가져다 쓴다.
    public function createBoardList($items)
    {
        $boardList = [];
        foreach($items as $item) {
            if( !array_has($boardList, $item->board_id) ) {
                $boardList = array_add($boardList, $item->board_id, Board::getBoard($item->board_id, 'id'));
            }
        }

        return $boardList;
    }

    // 한 페이지에서 한 사용자는 한번만 불러오도록 사용자 리스트를 만들어서 가져다 쓴다.
    public function createUserList($items)
    {
        $userList = [];
        foreach($items as $item) {
            if( !array_has($userList, $item->user_id) ) {
                $userList = array_add($userList, $item->user_id, $item->user_id ? User::getUser($item->user_id) : new User());
            }
        }

        return $userList;
    }

    // 새글 선택 삭제
    public function deleteWrites($ids)
    {
        $boardNews = BoardNew::select('board_news.*', 'boards.table_name')
                    ->leftJoin('boards', 'boards.id', '=', 'board_news.board_id')
                    ->whereIn('board_news.id', $ids)
                    ->get();
        $comment = new Comment();
        $point = new Point();
        $boardFile = new BoardFile();
        foreach($boardNews as $boardNew) {
            $boardId = $boardNew->board_id;
            $writeId = $boardNew->write_id;

            $writeModel = new Write();
            $writeModel->board = Board::getBoard($boardId, 'id');
            $writeModel->setTableName($boardNew->table_name);
            // 원글 삭제
            if($writeId == $boardNew->write_parent) {
                // 글쓰기에 부여된 포인트 삭제
                $point->deleteWritePoint($writeModel, $boardId, $writeId);
                $write = Write::getWrite($boardId, $writeId);
                if($write->file > 0) {
                    // 서버에서 파일 삭제 첨부파일의 썸네일 삭제, 파일 테이블에서 파일 정보 삭제
                    $result = $boardFile->deleteWriteAndAttachFile($boardId, $writeId);
                    if( array_search(false, $result) != false ) {
                        abort(500, '정상적으로 게시글을 삭제하는데 실패하였습니다.\\n('. $boardId. '게시판 '. $writeId. '번 글의 첨부 파일 삭제)');
                    }
                }
                // 게시글 삭제
                $result = $writeModel->deleteWrite($writeModel, $writeId);
                if($result <= 0) {
                    abort(500, '정상적으로 게시글을 삭제하는데 실패하였습니다.\\n('. $boardId. '게시판 '. $writeId. '번 글의 삭제)');
                }
            } else {    // 댓글 삭제
                $comment->deleteComment($writeModel, $boardId, $writeId);
            }
        }

    }

}
