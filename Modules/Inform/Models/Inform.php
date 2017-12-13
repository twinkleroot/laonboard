<?php

namespace Modules\Inform\Models;

use Illuminate\Support\Facades\Notification;
use Modules\Inform\Notifications\InvoiceWrited;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Board;
use App\Models\Config;
use Cache;

class Inform
{
    public $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    // 알림 보내기(데이터베이스 알림 사용)
    public function sendInform($writeModel, $writeId)
    {
        $board = $writeModel->board;
        $write = $writeModel->find($writeId);
        $parentWrite = $write->id == $write->parent ? $write : $writeModel->find($write->parent);
        $userEmail = [
            $board->admin,  // 게시판 관리자
            $board->group->admin,   // 그룹 관리자
            $superAdmin = cache('config.homepage')->superAdmin,    // 최고 관리자
            $parentWrite->email,    // 원글 게시자
        ];

        // 하위 댓글이라면 상위 댓글 쓴 사람에게도 알림 전송
        $commentLevel = mb_strlen($write->comment_reply, 'UTF-8');
        if($commentLevel > 0) {
            $parentCommentReply = mb_substr($write->comment_reply, 0, $commentLevel-2, 'UTF-8');
            $upperComment = $writeModel->where([
                                    'comment_reply' => $parentCommentReply,
                                    'parent' => $write->parent,
                                    'comment' => $write->comment
                                ])->first();
            $userEmail[] = $upperComment->email;
        }
        $uniqueEmail = array_values(array_unique(array_filter($userEmail)));
        if(auth()->check()) {
            $currentUserEmail = auth()->user()->email;
            $uniqueEmail = array_where($uniqueEmail, function ($value, $key) use ($currentUserEmail){
                return $value != $currentUserEmail;
            });
        }

        $users = User::whereIn('email', $uniqueEmail)->get();

        Notification::send($users, new InvoiceWrited($board, $write->id));
    }

    // 알림 내역 가져오기
    public function getInforms($request=null)
    {
        $informs;
        if(isset($request) && $request->filled('read')) {
            if($request->read == 'y') {
                $informs = auth()->user()->readNotifications;
            } else {
                $informs = auth()->user()->unreadNotifications;
            }
        } else {
            $informs = auth()->user()->notifications;
        }

        $boardList = [];
        $userList = [];
        foreach($informs as $inform) {
            $item = $inform->data;
            $boardList = $this->addBoardList($boardList, $item);
            $userList = $this->addUserList($userList, $item);
            $board = $boardList[$item['tableName']];
            $boardSubject = $board->subject;
            $nick = $userList[$item['writeUser']]->nick;
            if(!$nick) {
                $write = \App\Models\Write::getWrite($board->id, $item['writeId']);
                if($write) {
                    $nick = $write->name;
                } else {
                    $nick = '손';
                }
            }
            $parentSubject = subjectLength($item['parentSubject'], 10);
            $writeSubject = subjectLength($item['subject'], 10);
            if($item['isComment']) {
                $inform->subject = "{$nick}님이 {$boardSubject}게시판의 [{$parentSubject}] 글에 댓글 [{$writeSubject}]을 남기셨습니다.";
            } else {
                if($item['reply']) {
                    $inform->subject = "{$nick}님이 {$boardSubject}게시판에 답변글 [{$writeSubject}]을 남기셨습니다.";
                } else {
                    $inform->subject = "{$nick}님이 {$boardSubject}게시판에 글 [{$writeSubject}]을 남기셨습니다.";
                }
            }
        }

        // 수동으로 페이징할 땐 컬렉션을 잘라주어야 한다.
        $currentPage = $request->filled('page') ? $request->page : 1 ;
        $informPageRow = 10;
        $sliceInforms = $informs->slice($informPageRow * ($currentPage - 1), $informPageRow);
        $informs = new \App\Models\CustomPaginator($sliceInforms, notNullCount($informs), $informPageRow, $currentPage);

        return $informs;
    }

    // 한 페이지에서 한 게시판 및 그룹은 한번만 불러오도록 게시판 리스트를 만들어서 가져다 쓴다.
    public function addBoardList($boardList, $inform)
    {
        if( !array_key_exists($inform['tableName'], $boardList) ) {
            return array_add($boardList, $inform['tableName'], Board::getBoard($inform['tableName'], 'tableName'));
        }

        return $boardList;
    }

    // 한 페이지에서 한 사용자는 한번만 불러오도록 사용자 리스트를 만들어서 가져다 쓴다.
    public function addUserList($userList, $inform)
    {
        if( !array_key_exists($inform['writeUser'], $userList) ) {
            return array_add($userList, $inform['writeUser'], $inform['writeUser'] ? User::getUser($inform['writeUser']) : new User());
        }

        return $userList;
    }

    // 회원 알림 읽음 표시
    public function markAsReadInforms($ids)
    {
        $ids = explode(',', $ids);
        foreach($ids as $id) {
            auth()->user()->unreadNotifications->where('id', $id)->markAsRead();
        }
    }

    // 회원 알림 내역 삭제
    public function destroyInforms($request)
    {
        // 모든 알림 삭제
        if($request->filled('delType') && $request->delType == 'all') {
            foreach(auth()->user()->notifications as $inform) {
                $inform->delete();
            }
        } else {    // 선택 삭제
            $ids = explode(',', $request->ids);
            foreach($ids as $id) {
                auth()->user()->notifications->where('id', $id)->first()->delete();
            }
        }
    }

    // 회원 알림 읽음 표시
    public function markAsReadOne($id)
    {
        return ['result' => auth()->user()->unreadNotifications->where('id', $id)->markAsRead()];
    }

    public function updateAdmin($request)
    {
        Cache::forget("config.inform");

        $data = array_except($request->all(), ['_method', '_token']);
        $message = '';

        if($this->config->updateConfigByOne('inform', $data)) {
            $message = '알림 설정을 변경하였습니다.';
        }

        return $message;
    }

}
