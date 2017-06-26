<?php

namespace App\Http\Middleware;

use Closure;
use App\Board;
use DB;

class CanActionWriteImmediately
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $action)
    {
        $writeId = $request->writeId;
        $boardId = $request->boardId;
        $board = Board::find($boardId);
        $write = DB::table('write_'.$board->table_name)->where('id', $writeId)->first();

        if(session()->get(session()->getId(). $action. '_board_'. $boardId. '_write_'. $writeId)) {
            return $next($request);
        } else if( !$write->user_id && !session()->get('admin') ) {
            return redirect(route('board.password.check', camel_case('write_'.$action)). '?boardId='. $boardId. '&writeId='. $writeId. '&nextUrl='. $request->url());
        }

        return $next($request);
    }
}
