<?php

namespace App\Http\Middleware;

use Closure;
use App\Board;

class CheckRss
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
        $board = Board::getBoard($request->boardId);
        if($board->read_level >= 2) {
            return alert('비회원 읽기가 가능한 게시판만 RSS 지원합니다.');
        }
        if(!$board->use_rss_view) {
            return alert('RSS 보기가 금지되어 있습니다.');
        }

        return $next($request);
    }
}
