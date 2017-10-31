<?php

namespace App\Http\Middleware;

use Closure;

class CommentViewParent
{
    /**
     * Handle an incoming request.
     * 글 보기 할 때 요청한 경로의 글이 댓글이면 원글을 보여주도록
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $writeModel = app()->tagged('write')[0];
        $boardModel = app()->tagged('board')[0];

        $board = $boardModel::getBoard($request->boardName, 'table_name');
        $writeId = $request->writeId;

        $write = $writeModel::getWrite($board->id, $writeId);
        if($write->is_comment) {
            return redirect(route('board.view', ['boardId' => $request->boardName, 'writeId' => $write->parent])."#comment$writeId");
        }
        return $next($request);
    }
}
