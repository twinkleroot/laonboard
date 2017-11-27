<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
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

    public $board;

    public function __construct()
    {
        $this->table = 'board_news';
        $this->boardModel = app()->tagged('board')[0];
        $this->writeModel = app()->tagged('write')[0];
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
        $boardList = $this->createBoardList($boardNewList);
        $userList = $this->createUserList($boardNewList);
        $writeList = [];
        foreach($boardNewList as $boardNew) {
            $board = $boardList[$boardNew->board_id];
            $this->writeModel->setTableName($board->table_name);
            $write = '';
            // 한 페이지에서 모든 글은 한번만 select 하도록 하기 위해 글 리스트에 글을 담아 놓는다.
            if( !array_key_exists($board->table_name. $boardNew->write_id, $writeList) ) {
                $write = $this->writeModel->find($boardNew->write_id);
                $writeList = array_add($writeList, $board->table_name. $write->id, $write);
            } else {
                $write = $writeList[$board->table_name. $boardNew->write_id];
            }
            $subject = subjectLength($write->subject, 60);
            $user = $userList[$boardNew->user_id];
            // 회원 아이콘 경로 추가
            if($write->user_id && cache('config.join')->useMemberIcon) {
                $folder = getIconFolderName($user->created_at);
                $iconName = getIconName($user->id, $user->created_at);
                $iconPath = storage_path('app/public/user/'. $folder. '/'). $iconName. '.gif';
                if(File::exists($iconPath)) {
                    $write->iconPath = '/storage/user/'. $folder. '/'. $iconName. '.gif';
                }
            }
            // 원글, 댓글 공통 추가 데이터
            $boardNew->write = $write;

            if($boardNew->write_id != $boardNew->write_parent) {
                if(!array_key_exists($board->table_name. $boardNew->write_parent, $writeList)) {
                    $parentWrite = $this->writeModel->find($boardNew->write_parent);
                    $subject = subjectLength($parentWrite->subject, 60);
                    // 한 페이지에서 모든 글은 한번만 select 하도록 하기 위해 글 리스트에 글을 담아 놓는다.
                    $writeList = array_add($writeList, $board->table_name. $parentWrite->id, $parentWrite);
                } else {
                    $subject = subjectLength($writeList[$board->table_name. $boardNew->write_parent]->subject, 60);
                }
            }

            $boardNew->writeSubject = $subject;
            $boardNew->user_email = $user->email;
            $boardNew->user_id_hashkey = $user->id_hashkey;
            $boardNew->user_created_at = $user->created_at;
            $boardNew->commentTag = '';
            $boardNew->name = $write->name;

            // 댓글은 데이터 따로 추가
            if($boardNew->write_id != $boardNew->write_parent) {
                $comment = $writeList[$board->table_name. $boardNew->write_id];	 // 댓글
                $boardNew->writeSubject = '[코] '. $subject;    // [코] + 원글의 제목
                $boardNew->commentTag = '#comment'.$comment->id;
                // $boardNew->write->created_at = $comment->created_at;
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
            if( !array_key_exists($item->board_id, $boardList) ) {
                $boardList = array_add($boardList, $item->board_id, $this->boardModel::getBoard($item->board_id));
            }
        }

        return $boardList;
    }

    // 한 페이지에서 한 사용자는 한번만 불러오도록 사용자 리스트를 만들어서 가져다 쓴다.
    public function createUserList($items)
    {
        $userList = [];
        foreach($items as $item) {
            if( !array_key_exists($item->user_id, $userList) ) {
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
        $boardFile = new BoardFile();
        foreach($boardNews as $boardNew) {
            $boardId = $boardNew->board_id;
            $writeId = $boardNew->write_id;

            $this->writeModel->board = $this->boardModel::getBoard($boardId);
            $this->writeModel->setTableName($boardNew->table_name);
            // 원글 삭제
            if($writeId == $boardNew->write_parent) {
                // 글쓰기에 부여된 포인트 삭제
                deleteWritePoint($this->writeModel, $boardId, $writeId);
                $write = $this->writeModel::getWrite($boardId, $writeId);
                if($write->file > 0) {
                    // 서버에서 파일 삭제 첨부파일의 썸네일 삭제, 파일 테이블에서 파일 정보 삭제
                    $result = $boardFile->deleteWriteAndAttachFile($boardId, $writeId);
                    if(!$result) {
                        abort(500, '정상적으로 게시글을 삭제하는데 실패하였습니다.\\n('. $boardId. '게시판 '. $writeId. '번 글의 첨부 파일 삭제)');
                    }
                }
                // 게시글 삭제
                $result = $this->writeModel->deleteWrite($this->writeModel, $writeId);
                if($result <= 0) {
                    abort(500, '정상적으로 게시글을 삭제하는데 실패하였습니다.\\n('. $boardId. '게시판 '. $writeId. '번 글의 삭제)');
                }
            } else {    // 댓글 삭제
                $comment->deleteComment($this->writeModel, $boardId, $writeId);
            }
        }

    }

}
