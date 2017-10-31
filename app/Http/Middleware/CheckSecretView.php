<?php

namespace App\Http\Middleware;

use Closure;

class CheckSecretView
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
        $boardId = $board->id;
        $writeId = $request->writeId;

        $write = $writeModel::getWrite($boardId, $writeId);

        if(str_contains($write->option, 'secret')) {
            $user = auth()->user();
            // 비회원이 아니면서 작성자 본인 or 관리자면 패스
            $userId = !$user ? 0 : $user->id;
            if( (auth()->check() && $userId == $write->user_id) || session()->get('admin')) {
            } else if(!session()->get(session()->getId(). 'secret_board_'.$request->boardName.'_write_'.$writeId)) {
                // 비밀번호 입력 폼으로 연결
                return redirect(route('board.password.check', 'secret'). '?boardName='. $request->boardName. '&writeId='. $writeId. '&nextUrl='. $request->fullUrl());
            }
        }

        return $next($request);
    }
}
