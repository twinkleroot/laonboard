<?php

namespace App\Http\Middleware;

use Closure;
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
        $message = '';

        $baseLevel = 1; // 비회원
        if(auth()->check()) {
            $baseLevel = auth()->user()->level;  // 유저의 등급을 넣음
        }

        $boardModel = app()->tagged('board')[0];
        $boardName = $request->segments()[1];
        $board = $boardModel::getBoard($boardName, 'table_name');

        if($baseLevel < $board[$type]) {
            if(str_contains($type, 'list')) {
                $message = '목록을 볼 권한이 없습니다.';
            } else if(str_contains($type, 'read')) {
                $message = '글을 읽을 권한이 없습니다.';
            } else if(str_contains($type, 'write')) {
                $message = '글을 쓸 권한이 없습니다.';
            } else if(str_contains($type, 'reply')) {
                $message = '답변을 쓸 권한이 없습니다.';
            } else if(str_contains($type, 'update')) {
                $message = '글을 수정할 권한이 없습니다.';
            } else if(str_contains($type, 'comment')) {
                $message = '댓글을 쓸 권한이 없습니다.';
            } else if(str_contains($type, 'delete')) {
                $message = '글을 삭제할 권한이 없습니다.';
            } else if(str_contains($type, 'download')) {
                $message = '파일 다운로드 권한이 없습니다.';
            } else if(str_contains($type, 'upload')) {
                $message = '파일 업로드 권한이 없습니다.';
            } else if(str_contains($type, 'link')) {
                $message = '링크에 연결할 권한이 없습니다.';
            }

            if(auth()->check()) {
                return alert($message);
            } else {
                return alertRedirect($message, '/login?nextUrl='. $request->getRequestUri());
            }
        }
        return $next($request);
    }
}
