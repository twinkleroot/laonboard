<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class MailController extends Controller
{
    // 인증 메일 클릭했을 때 처리하기
    public function emailCertify(Request $request, $id, $crypt)
    {
        $user = getUser($id);

        $message = '메일인증 요청 정보가 올바르지 않습니다.';
        if($user->email_certify2 == $crypt) {
            if($user->update([
                'email_certify' => Carbon::now(),
                'email_certify2' => null,
                'level' => cache("config.join")->joinLevel,
            ])) {
                $message = '메일인증 처리를 완료하였습니다. \\n\\n지금부터 회원님은 사이트를 원활하게 이용하실 수 있습니다.';
            }
        }
        return alertClose($message);
    }

    public function send(Request $request)
    {

    }
}
