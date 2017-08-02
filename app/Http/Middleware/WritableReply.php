<?php

namespace App\Http\Middleware;

use Closure;
use Cache;
use App\Board;
use App\Write;

class WritableReply
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $boardId = $request->segment(2);    // uri의 2번째 항목
        $writeId = $request->segment(4);    // uri의 4번째 항목
        if(strpos($request->getRequestUri(), 'reply')) {
            $user = auth()->user();
            $board = Board::getBoard($boardId);
            $notices = explode(',', $board->notice);

            $write = Write::getWrite($boardId, $writeId);
            if(str_contains($write->option, 'secret')) {
                if(!$user && ($user && !$user->isBoardAdmin($board)) && ($user && $user->id != $write->user_id)) {
                    return alert('비밀글에는 자신 또는 관리자만 답변이 가능합니다.');
                }
            }
            if (in_array((int)$writeId, $notices)) {
               return alert('공지에는 답변 할 수 없습니다.');
            } else if ($user->level < $board->reply_level) {
               return alert('글을 답변할 권한이 없습니다.');
            } else if (!is_null($write) && strlen($write->reply) == 10) { // 최대 답변은 테이블에 잡아놓은 wr_reply 사이즈만큼만 가능합니다.
               return alert('더 이상 답변하실 수 없습니다.\\n답변은 10단계 까지만 가능합니다.');
            }

        }
        return $next($request);
    }
}
