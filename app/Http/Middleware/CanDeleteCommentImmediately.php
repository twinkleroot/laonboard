<?php

namespace App\Http\Middleware;

use Closure;
use App\Board;
use DB;

class CanDeleteCommentImmediately
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
        $writeId = $request->writeId;
        $commentId = $request->commentId;
        $board = Board::getBoard($request->boardName, 'table_name');
        $comment = DB::table('write_'.$board->table_name)->where('id', $commentId)->first();

        if(session()->get(session()->getId(). 'delete_board_'. $request->boardName. '_write_'. $commentId)) {
            return $next($request);
        } else if( !$comment->user_id && !session()->get('admin') ) {
            return redirect(route('board.password.check', 'commentDelete'). '?boardId='. $request->boardName. '&writeId='. $writeId. '&commentId='. $commentId. '&nextUrl='. $request->fullUrl());
        }

        return $next($request);
    }
}
