<?php

namespace App\Http\Middleware;

use Closure;

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
        $boardModel = app()->tagged('board')[0];
        $board = $boardModel::getBoard($request->boardId);

        if(auth()->user()->isBoardAdmin($board)) {
            return $next($request);
        }

        return alert('게시판관리자만 접근 가능합니다.');
    }
}
