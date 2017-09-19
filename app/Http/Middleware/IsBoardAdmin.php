<?php

namespace App\Http\Middleware;

use Closure;
use App\Board;

class IsBoardAdmin
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
        if(auth()->user()->isBoardAdmin($board)) {
            return $next($request);
        }

        return alert('게시판관리자만 접근 가능합니다.');
    }
}
