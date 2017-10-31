<?php

namespace App\Http\Middleware;

use Closure;

class CheckValidWrite
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
        $writeModel = app()->tagged('write')[0];

        $board = $boardModel::getBoard($request->boardName, 'table_name');
        $write = $writeModel::getWrite($board->id, $request->writeId);

        if( !$write ) {
            return alertRedirect('글이 존재하지 않습니다.\\n글이 삭제되었거나 이동하였을 수 있습니다.', "/bbs/{$request->boardName}");
        }

        return $next($request);
    }
}
