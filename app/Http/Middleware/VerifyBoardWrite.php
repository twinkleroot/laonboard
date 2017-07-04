<?php

namespace App\Http\Middleware;

use Closure;
use App\Common\Util;

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
        if( !isset($request->subject)) {
            $message = '제목을 입력해 주세요.';
            return redirect(route('message'))->with('message', $message);
        }
        if( !isset($request->content)) {
            $message = '내용을 입력해 주세요.';
            return redirect(route('message'))->with('message', $message);
        }
        // 글 내용 검사
        if( !Util::checkIncorrectContent($request) ) {
            $message = '내용에 올바르지 않은 코드가 다수 포함되어 있습니다.';
            return redirect(route('message'))->with('message', $message);
        }
        // Post로 넘어온 데이터 크기 검사
        if( !Util::checkPostMaxSize($request) ) {
            $message = '파일 또는 글내용의 크기가 서버에서 설정한 값을 넘어 오류가 발생하였습니다.\\npost_max_size='.ini_get('post_max_size').' , upload_max_filesize='.ini_get('upload_max_filesize').'\\n게시판관리자 또는 서버관리자에게 문의 바랍니다.';
            return redirect(route('message'))->with('message', $message);
        }
        // 관리자가 아닌데 공지사항을 남기려 하는 경우가 있는지 검사
        if( !Util::checkAdminAboutNotice($request) ) {
            $message = '관리자만 공지할 수 있습니다.';
            return redirect(route('message'))->with('message', $message);
        }

        return $next($request);
    }



}
