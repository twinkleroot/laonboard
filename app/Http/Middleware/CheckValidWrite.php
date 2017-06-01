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
        $article = Cache::rememberForever("board.{$request->boardId}.write.{$request->writeId}", function() use($request) {
            $write = new Write($request->boardId);
            $write->setTableName($write->board->table_name);
            return $write->find($request->writeId);
        });

        if( is_null($article) ) {
            return redirect(route('message'))
               ->with('message', '글이 존재하지 않습니다.\\n글이 삭제되었거나 이동하였을 수 있습니다.')
               ->with('redirect', '/');
        }

        return $next($request);
    }
}
