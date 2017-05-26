<?php

namespace App\Http\Middleware;

use App\Write;
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
        $write = new Write($request->boardId);
        $write->setTableName($write->board->table_name);

        if( is_null($write->find($request->writeId)) ) {
            return redirect(route('message'))
               ->with('message', '삭제되거나 이동된 글입니다.')
               ->with('redirect', '/');
        }

        return $next($request);
    }
}
