<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Cache;
use App\Board;

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
        $boardId = $request->boardId;
        $writeId = $request->writeId;

        $board = Board::find($boardId);
        $write = DB::table('write_'. $board->table_name)->find($writeId);

        if(str_contains($write->option, 'secret')) {
            $user = auth()->user();
            // 비회원이 아니면서 작성자 본인 or 관리자면 패스
            $userId = !$user ? 0 : $user->id;
            if( ( !auth()->guest() && $userId == $write->user_id) || session()->get('admin')) {
                ;
            } else if(!session()->get(session()->getId(). 'secret_board_'.$boardId.'_write_'.$writeId)) {
                // 비밀번호 입력 폼으로 연결
                return redirect(route('board.password.check', 'secret'). '?boardId='. $boardId. '&writeId='. $writeId. '&nextUrl='. $request->url());
            }
        }

        return $next($request);
    }
}
