<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Config;
use Carbon\Carbon;
use App\Mail\EmailCertify;


class MailController extends Controller
{
    public $config;

    public function __construct(Config $config)
    {
        $this->config = Config::getConfig('config.join');
    }
    // 인증 메일 클릭했을 때 처리하기
    public function emailCertify(Request $request, $id, $crypt)
    {
        $user = User::where([
            'id_hashkey' => $id
        ])->first();

        $message = '';
        if($user->email_certify2 == $crypt) {
            $user->update([
                'email_certify' => Carbon::now(),
                'email_certify2' => null,
                'level' => $this->config->joinLevel,
            ]);
            $message = '메일인증 처리를 완료하였습니다. \\n\\n지금부터 회원님은 사이트를 원활하게 이용하실 수 있습니다.';
        } else {
            $message = '메일인증 요청 정보가 올바르지 않습니다.';
        }
        return redirect(route('message'))->with('message', $message);
    }
}
