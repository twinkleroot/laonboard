<?php

namespace App\Http\Middleware;

use Closure;

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
        $boardModel = app()->tagged('board')[0];
        $writeModel = app()->tagged('write')[0];

        $board = $boardModel::getBoard($request->boardName, 'table_name');
        $writeId = $request->writeId;
        $write = $writeModel::getWrite($board->id, $writeId);

        if(session()->get(session()->getId(). $action. '_board_'. $board->table_name. '_write_'. $writeId)) {
            return $next($request);
        } else if( !$write->user_id && !session()->get('admin') ) {
            return redirect(route('board.password.check', camel_case('write_'.$action)). '?boardName='. $board->table_name. '&writeId='. $writeId. '&nextUrl='. $request->fullUrl());
        }

        return $next($request);
    }
}
