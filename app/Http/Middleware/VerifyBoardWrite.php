<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use App\Config;

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
        $hasMessage = 0;
        if( !isset($request->subject)) {
            $hasMessage = 1;
            $message = '제목을 입력해 주세요.';
        }
        if( !isset($request->content)) {
            $hasMessage = 1;
            $message = '내용을 입력해 주세요.';
        }
        // 글쓰기 간격 검사
        if( !$this->checkWriteInterval() ) {
            $hasMessage = 1;
            $message = '너무 빠른 시간내에 게시물을 연속해서 올릴 수 없습니다.';
        }
        // 글 내용 검사
        if( !$this->checkIncorrectContent($request) ) {
            $hasMessage = 1;
            $message = '내용에 올바르지 않은 코드가 다수 포함되어 있습니다.';
        }
        // Post로 넘어온 데이터 크기 검사
        if( !$this->checkPostMaxSize($request) ) {
            $hasMessage = 1;
            $message = '파일 또는 글내용의 크기가 서버에서 설정한 값을 넘어 오류가 발생하였습니다.\\npost_max_size='.ini_get('post_max_size').' , upload_max_filesize='.ini_get('upload_max_filesize').'\\n게시판관리자 또는 서버관리자에게 문의 바랍니다.';
        }
        //
        if( !$this->checkAdminAboutNotice($request) ) {
            $hasMessage = 1;
            $message = '관리자만 공지할 수 있습니다.';
        }

        if($hasMessage == 1) {
            return redirect(route('message'))->with('message', $message);
        }

        return $next($request);
    }

    // 글쓰기 간격 검사
    private function checkWriteInterval()
    {
        $dt = Carbon::now();
        $interval = Config::getConfig('config.board')->delaySecond;

        if(session()->has('postTime')) {
            if(session()->get('postTime') >= $dt->subSecond($interval) && !session()->get('admin')) {
                return false;
            }
        }
        session()->put('postTime', Carbon::now());

        return true;
    }

    // 올바르지 않은 코드가 글 내용에 다수 들어가 있는지 검사
    private function checkIncorrectContent($request)
    {
        if (substr_count($request->content, '&#') > 50) {
            return false;
        }
        return true;
    }

    // 서버에서 지정한 Post의 최대 크기 검사
    private function checkPostMaxSize($request)
    {
        if (empty($_POST)) {
            return false;
        }
        return true;
    }

    // 관리자가 아닌데 공지사항을 남기려 하는 경우가 있는지 검사
    private function checkAdminAboutNotice($request)
    {
        if ( !session()->get('admin') && $request->has('notice') ) {
    		return false;
        }
        return true;
    }

}
