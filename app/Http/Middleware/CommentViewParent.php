<?php

namespace App\Http\Middleware;

use Closure;
use App\Board;
use App\Write;

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
        $boardId = $request->boardId;
        $writeId = $request->writeId;

        $board = Board::find($boardId);
        $writeModel = new Write($boardId);
        $writeModel->setTableName($board->table_name);
        $write = $writeModel->where('id', $writeId)->first();
        if($write->is_comment) {
            return redirect(route('board.view', ['boardId' => $boardId, 'writeId' => $write->parent])."#comment$writeId");
        }
        return $next($request);
    }
}
