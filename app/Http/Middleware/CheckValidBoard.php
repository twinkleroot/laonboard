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
            return redirect(route('message'))
               ->with('message', '잘못된 경로입니다. 다시 확인해 주세요.')
               ->with('redirect', '/');
        }

        return $next($request);
    }
}
