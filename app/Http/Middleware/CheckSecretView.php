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

        $board = Cache::rememberForever("board.{$boardId}", function() use($boardId) {
            return Board::find($boardId);
        });
        $write = Cache::rememberForever("board.{$boardId}.write.{$writeId}", function() use($board, $writeId) {
            return DB::table('write_'. $board->table_name)->find($writeId);
        });

        $user = auth()->user();

        if(str_contains($write->option, 'secret')) {
            // 관리자나 작성자 본인이면 패스
            if($user->id == $write->user_id || session()->get('admin')) {
                ;
            } else if(!session()->get('secret_board_'.$boardId.'_write_'.$writeId)) {
                session()->put('nextUri', $request->server('REQUEST_URI'));  // 비밀번호 검사 후 이동할 URI를 세션에 넣는다.
                // 비밀번호 확인
                return redirect(route('board.password', [
                        'boardId' => $boardId,
                        'writeId' => $writeId,
                ]));
            }
        }

        return $next($request);
    }
}
