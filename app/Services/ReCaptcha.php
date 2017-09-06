<?php

namespace App\Services;

use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;

class ReCaptcha
{
    /*
    * 구글 리캡챠 서버쪽 검사
    *
    * @return boolean
    */
    public static function reCaptcha(Request $request) {
        $url =
            'https://www.google.com/recaptcha/api/siteverify'. '?secret='. cache('config.sns')->googleRecaptchaServer. '&response='. $request['g-recaptcha-response'];
        $flag = json_decode(Curl::to($url)->get());

        if(!$flag || !$flag->success) {
            abort(500, '자동등록방지 입력이 틀렸습니다. 다시 입력해 주십시오.');
        }

        return $flag->success;
    }

}
