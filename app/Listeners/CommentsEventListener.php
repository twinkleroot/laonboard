<?php

namespace App\Listeners;

use App\Events\UpdateComment;
use App\Events\CreateComment;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CommentsEventListener
{
    public $boardModel;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->boardModel = app()->tagged('board')[0];
    }

    /**
     * Handle the event.
     *
     * @param  CreateComment  $event
     * @return void
     */
    public function handle(CreateComment $event)
    {

    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe(\Illuminate\Events\Dispatcher $events)
    {
        $events->listen(
            \App\Events\CreateComment::class,
            __CLASS__. '@createComment'
        );

        $events->listen(
            \App\Events\UpdateComment::class,
            __CLASS__. '@updateComment'
        );
    }

    public function createComment(\App\Events\CreateComment $event)
    {
        $request = $event->request;
        $user = auth()->user();
        $userPoint = !$user ? 0 : $user->point;
        $board = $this->boardModel::getBoard($request->boardName, 'table_name');
        // 댓글 쓰기 포인트 설정시 포인트 검사
        $tmpPoint = $userPoint > 0 ? $userPoint : 0;
        if($tmpPoint + $board->comment_point < 0 && !session()->get('admin')) {
            $message = '보유하신 포인트('.number_format($userPoint).')가 없거나 모자라서 댓글쓰기('.number_format($board->comment_point).')가 불가합니다.\\n\\n포인트를 적립하신 후 다시 댓글을 써 주십시오.';
            abort(500, $message);
        }
        // 글 내용 검사
        if( !checkIncorrectContent($request) ) {
            $message = '내용에 올바르지 않은 코드가 다수 포함되어 있습니다.';
            abort(500, $message);
        }
    }

    public function updateComment(\App\Events\UpdateComment $event)
    {
        // 글 내용 검사
        if( !checkIncorrectContent($event->request) ) {
            $message = '내용에 올바르지 않은 코드가 다수 포함되어 있습니다.';
            abort(500, $message);
        }
    }
}
