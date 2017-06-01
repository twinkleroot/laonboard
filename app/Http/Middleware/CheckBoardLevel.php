<?php

namespace App\Http\Middleware;

use Closure;
use Cache;
use App\Board;
use Exception;

class CheckBoardLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $type)
    {
        $user = auth()->user();
        $message = '';

        $baseLevel = 1; // 비회원
        if( !is_null($user) ) {
            $baseLevel = $user->level;  // 유저의 등급을 넣음
        }

        $boardId = $request->segments()[1];
        $board = Cache::rememberForever("board.{$boardId}", function() use($boardId) {
            return Board::find($boardId);
        });

        if($baseLevel < $board[$type]) {
            if(str_contains($type, 'list')) {
                $message = '목록을 볼 권한이 없습니다.';
            } else if(str_contains($type, 'read')) {
                $message = '글을 읽을 권한이 없습니다.';
            } else if(str_contains($type, 'write')) {
                $message = '글을 쓸 권한이 없습니다.';
            } else if(str_contains($type, 'update')) {
                $message = '글을 수정할 권한이 없습니다.';
            } else if(str_contains($type, 'comment')) {
                $message = '댓글을 쓸 권한이 없습니다.';
            } else if(str_contains($type, 'delete')) {
                $message = '글을 삭제할 권한이 없습니다.';
            } else if(str_contains($type, 'download')) {
                $message = '파일 다운로드 권한이 없습니다.';
            }

            if(!is_null($user)) {
                return redirect(route('message'))->with('message', $message);
            } else {
                return redirect(route('message'))->with('message', $message)->with('redirect', '/login');
            }
        }
        return $next($request);
    }
}
