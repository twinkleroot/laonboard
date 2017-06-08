<?php

namespace App\Http\Controllers\Board;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Cache;
use App\Notification;
use App\Write;
use App\Comment;

class CommentController extends Controller
{
    public $writeModel;
    public $comment;
    public $notification;

    public function __construct(Request $request, Comment $comment, Notification $notification)
    {
        $this->writeModel = new Write($request->boardId);
        if( !is_null($this->writeModel->board) ) {
            $this->writeModel->setTableName($this->writeModel->board->table_name);
        }

        $this->comment = $comment;
        $this->notification = $notification;
    }

    // 댓글 저장
    public function store(Request $request)
    {
        $result = $this->comment->storeComment($this->writeModel, $request);
        if(isset($result['message'])) {
            return view('message', [
                'message' => $result['message']
            ]);
        }

        if(Cache::get('config.email.default')->emailUse && $this->writeModel->board->use_email) {
            $this->notification->sendWriteNotification($this->writeModel, $result);
        }

        return redirect($request->requestUri. '#comment'. $result);
    }

    // 댓글 수정
    public function update(Request $request)
    {
        $result = $this->comment->updateComment($this->writeModel, $request);

        if(!$result) {
            return view('message', [
                'message' => '댓글 수정에 실패하였습니다.'
            ]);
        }

        return redirect($request->requestUri. '#comment'. $request->commentId);
    }

    // 댓글 삭제
    public function destroy(Request $request, $boardId, $commentId)
    {
        $result = $this->comment->deleteComment($this->writeModel, $request, $commentId);

        if(isset($result['message'])) {
            return view('message', [
                'message' => $result['message']
            ]);
        }

        return redirect()->back();
    }
}
