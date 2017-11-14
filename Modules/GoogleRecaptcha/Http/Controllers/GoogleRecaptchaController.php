<?php

namespace Modules\GoogleRecaptcha\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Ixudra\Curl\Facades\Curl;

class GoogleRecaptchaController extends Controller
{
    /**
     * 구글 리캡챠 서버쪽 검사
     * @return Response
     */
    public function recaptcha(Request $request)
    {
        $url = 'https://www.google.com/recaptcha/api/siteverify'.
                '?secret='. cache('config.sns')->googleRecaptchaServer."11".
                '&response='. $request['g-recaptcha-response'];
        $flag = json_decode(Curl::to($url)->get());

        $message = '';
        if(!$flag || !$flag->success) {
            $message = '자동등록방지 입력이 틀렸습니다. 다시 입력해 주십시오.';
        }

        return [
            'message' => $message
        ];
    }

}
