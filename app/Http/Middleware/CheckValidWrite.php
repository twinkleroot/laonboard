<?php

namespace App\Http\Middleware;

use Cache;
use Closure;
use App\Write;

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
        $write = new Write($request->boardId);
        $write->setTableName($write->board->table_name);

        if( is_null($write->find($request->writeId)) ) {
            return redirect(route('message'))
               ->with('message', '글이 존재하지 않습니다.\\n글이 삭제되었거나 이동하였을 수 있습니다.');
        }

        return $next($request);
    }
}
