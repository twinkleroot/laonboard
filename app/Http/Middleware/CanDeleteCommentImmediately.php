<?php

namespace App\Http\Middleware;

use Closure;

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
        $boardModel = app()->tagged('board')[0];
        $writeModel = app()->tagged('write')[0];

        $board = $boardModel::getBoard($request->boardName, 'table_name');
        $comment = $writeModel::getWrite($board->id, $commentId);

        if(session()->get(session()->getId(). 'delete_board_'. $request->boardName. '_write_'. $commentId)) {
            return $next($request);
        } else if( !$comment->user_id && !session()->get('admin') ) {
            return redirect(route('board.password.check', 'commentDelete'). '?boardName='. $request->boardName. '&writeId='. $writeId. '&commentId='. $commentId. '&nextUrl='. $request->fullUrl());
        }

        return $next($request);
    }
}
