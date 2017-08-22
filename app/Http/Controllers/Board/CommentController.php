<?php

namespace App\Http\Controllers\Board;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notification;
use App\Write;
use App\Board;
use App\Comment;
use App\ReCaptcha;

class CommentController extends Controller
{
    public $writeModel;
    public $comment;
    public $notification;

    public function __construct(Request $request, Comment $comment, Write $write)
    {
        $this->writeModel = $write;
        $this->writeModel->board = Board::getBoard($request->boardName, 'table_name');
        $this->writeModel->setTableName($request->boardName);
        $this->comment = $comment;
    }

    // 댓글 저장
    public function store(Request $request)
    {
        if(auth()->guest() || (!auth()->user()->isSuperAdmin() && $this->writeModel->board->use_recaptcha)) {
            ReCaptcha::reCaptcha($request);
        }
        $result = $this->comment->storeComment($this->writeModel, $request);

        if(cache('config.email.default')->emailUse && $this->writeModel->board->use_email) {
            $notification = new Notification;
            $notification->sendWriteNotification($this->writeModel, $result);
        }

        return redirect($request->requestUri. '#comment'. $result);
    }

    // 댓글 수정
    public function update(Request $request)
    {
        $id = $this->comment->updateComment($this->writeModel, $request);

        return redirect($request->requestUri. '#comment'. $id);
    }

    // 댓글 삭제
    public function destroy(Request $request, $boardName, $writeId, $commentId)
    {
        $this->comment->deleteComment($this->writeModel, $boardName, $commentId);

        return redirect(route('board.view', ['boardName' => $boardName, 'writeId' => $writeId]));
    }
}
