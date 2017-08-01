<?php

namespace App\Http\Middleware;

use Closure;
use App\Board;

class isBoardAdmin
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
        $user = auth()->user();
        $board = Board::find($request->boardId);
        if($user->isBoardAdmin($board)) {
            return $next($request);
        }

        return alertRedirect('게시판관리자만 접근 가능합니다.');
    }
}
