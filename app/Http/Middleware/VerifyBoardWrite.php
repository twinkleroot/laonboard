<?php

namespace App\Http\Middleware;

use Closure;

class VerifyBoardWrite
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
        // 글 내용 검사
        if( !checkIncorrectContent($request) ) {
            $message = '내용에 올바르지 않은 코드가 다수 포함되어 있습니다.';
            return alert($message);
        }
        // 관리자가 아닌데 공지사항을 남기려 하는 경우가 있는지 검사
        if( !checkAdminAboutNotice($request) ) {
            $message = '관리자만 공지할 수 있습니다.';
            return alert($message);
        }

        $message = '';
        $baseLevel = 1; // 비회원
        $user = auth()->user();

        if($user) {
            $baseLevel = $user->level;  // 유저의 등급을 넣음
        }

        $boardName = $request->segment(2);
        $boardModel = app()->tagged('board')[0];
        $board = $boardModel::getBoard($boardName, 'table_name');

        // 파일 업로드 권한 있는지 검사
        if(notNullCount($request->attach_file) > 0) {
            if($baseLevel < $board->upload_level) {
                $message = '파일 업로드 권한이 없습니다.';
            }
        }

        // 링크를 걸 권한이 있는지 검사
        if($request->link1 || $request->link2) {
            if($baseLevel < $board->link_level) {
                $message = '링크를 걸 권한이 없습니다.';
            }
        }

        if($message) {
            if($user) {
                return alert($message);
            } else {
                return alertRedirect($message, '/login?nextUrl=/bbs/'. $boardName. '/create');
            }
        }

        return $next($request);
    }

}
