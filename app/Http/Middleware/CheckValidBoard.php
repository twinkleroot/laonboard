<?php

namespace App\Http\Middleware;

use Closure;
use Cache;
use App\Board;

class CheckValidBoard
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
        $board = Board::find($request->boardId);
        if(is_null($board)) {
            return alert('존재하지 않는 게시판입니다. 경로를 확인해 주세요.');
        }

        return $next($request);
    }
}
