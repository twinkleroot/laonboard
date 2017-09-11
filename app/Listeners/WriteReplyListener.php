<?php

namespace App\Listeners;

use App\Events\WriteReply;
use App\Board;
use App\Write;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class WriteReplyListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  WriteReply  $event
     * @return void
     */
    public function handle(WriteReply $event)
    {
        $request = $event->request;
        if(strpos($request->getRequestUri(), 'reply')) {	// 글 답변일 경우
            $boardName = $event->boardName;
            $writeId = $event->writeId;
            $user = auth()->user();
            $level = auth()->check() ? $user->level : 1;
            $board = Board::getBoard($boardName, 'table_name');
            $notices = explode(',', $board->notice);

            $write = Write::getWrite($board->id, $writeId);
            if(str_contains($write->option, 'secret')) {
                if(auth()->check() && !$user->isBoardAdmin($board) && $user->id != $write->user_id) {
                    abort(500, '비밀글에는 자신 또는 관리자만 답변이 가능합니다.');
                }
            }
            if (in_array((int)$writeId, $notices)) {
               abort(500,'공지에는 답변 할 수 없습니다.');
            } else if ($write && strlen($write->reply) == 10) { // 최대 답변은 테이블에 잡아놓은 wr_reply 사이즈만큼만 가능합니다.
               abort(500,'더 이상 답변하실 수 없습니다.\\n답변은 10단계 까지만 가능합니다.');
            }

        }
    }
}
