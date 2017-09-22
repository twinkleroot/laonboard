<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notification;
use App\Write;
use App\Board;
use App\Comment;
use App\Services\ReCaptcha;

class CommentsController extends Controller
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
        if(!auth()->check() ||
            (!auth()->user()->isBoardAdmin($this->writeModel->board)
             && $this->writeModel->board->use_recaptcha
             && todayWriteCount(auth()->user()->id) > config('gnu.todayWriteCount')
            )) {
            ReCaptcha::reCaptcha($request);
        }

        $rules = $this->rules();
        $messages = $this->messages();

        if(auth()->guest()) {
            $rules = array_add($rules, 'userName', 'required|alpha_dash|max:20');
            $rules = array_add($rules, 'password', 'required|max:20');
        }

        if($this->writeModel->board->write_min) {
            $rules['content'] .= '|min:'.$this->writeModel->board->write_min;
        }
        if($this->writeModel->board->write_max) {
            $rules['content'] .= '|max:'.$this->writeModel->board->write_max;
        }

        // 공백 제거
        $request->merge([
            'content' => trim($request->content)
        ]);

        $this->validate($request, $rules, $messages);

        event(new \App\Events\CreateComment($request));

        $comment = $this->comment->storeComment($this->writeModel, $request);

        // 기본환경설정에서 이메일 사용을 하고, 해당 게시판에서 메일발송을 사용하면
        if(cache('config.email.default')->emailUse && $this->writeModel->board->use_email) {
            $notification = new Notification;
            $notification->sendWriteNotification($this->writeModel, $comment->id);
        }

        return redirect($request->requestUri. '#comment'. $comment->id);
    }

    // 댓글 수정
    public function update(Request $request)
    {
        $rules = $this->rules();
        $messages = $this->messages();

        if(auth()->guest()) {
            $rules = array_add($rules, 'userName', 'required|alpha_dash|max:20');
            $rules = array_add($rules, 'password', 'required|max:20');
        }

        if($this->writeModel->board->write_min) {
            $rules['content'] .= '|min:'.$this->writeModel->board->write_min;
        }
        if($this->writeModel->board->write_max) {
            $rules['content'] .= '|max:'.$this->writeModel->board->write_max;
        }

        // 공백 제거
        $request->merge([
            'content' => trim($request->content)
        ]);

        $this->validate($request, $rules, $messages);

        event(new \App\Events\UpdateComment($request));

        $id = $this->comment->updateComment($this->writeModel, $request);

        return redirect($request->requestUri. '#comment'. $id);
    }

    // 댓글 삭제
    public function destroy(Request $request, $boardName, $writeId, $commentId)
    {
        $this->comment->deleteComment($this->writeModel, $boardName, $commentId);

        return redirect(route('board.view', ['boardName' => $boardName, 'writeId' => $writeId]));
    }

    // 유효성 검사 규칙
    public function rules()
    {
        return [
            'content' => 'required',
        ];
    }

    // 에러 메세지
    public function messages()
    {
        return [
            'userName.required' => '이름을 입력해 주세요.',
            'userName.alpha_dash' => '이름에 영문자, 한글, 숫자, 대쉬(-), 언더스코어(_)만 입력해 주세요.',
            'userName.max' => '이름은 :max자리를 넘길 수 없습니다.',
            'password.required' => '비밀번호를 입력해 주세요.',
            'password.max' => '비밀번호는 :max자리를 넘길 수 없습니다.',
            'content.required' => '댓글을 입력해 주세요.',
            'content.min' => '댓글은 :min글자 이상 쓰셔야 합니다.',
            'content.max' => '댓글은 :max글자 이하로 쓰셔야 합니다.',
        ];
    }
}
