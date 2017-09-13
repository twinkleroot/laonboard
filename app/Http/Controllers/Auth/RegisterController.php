<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\Validator;
// use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use Cache;
use App\User;
use App\Cert;
use App\Services\ReCaptcha;
use App\Admin\Config;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    // 라라벨에서 기본으로 제공해 주는 회원가입 트레이트 사용 안함.
    // use RegistersUsers;

    public $userModel;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(User $userModel)
    {
        $this->middleware('guest');

        $this->userModel = $userModel;
    }

    // 회원 가입 약관 페이지
    public function join()
    {
        $skin = cache("config.join")->skin ? : 'default';

        return viewDefault("user.$skin.join");
    }

    // 회원 가입 폼
    public function registerForm(Request $request)
    {
        $rules = [
            'agreeStipulation' => 'required',
            'agreePrivacy' => 'required',
        ];

        $messages = [
            'agreeStipulation.required' => ':attribute에 동의해야 회원가입을 진행할 수 있습니다.',
            'agreePrivacy.required' => ':attribute에 동의해야 회원가입을 진행할 수 있습니다.',
        ];

        $attributes = [
            'agreeStipulation' => '회원가입약관',
            'agreePrivacy' => '개인정보처리방침안내',
        ];

        $this->validate($request, $rules, $messages, $attributes);

        if(!cache('config.sns')->googleRecaptchaClient || !cache('config.sns')->googleRecaptchaServer) {
            return alertRedirect('자동등록방지 키가 등록되지 않아서 회원가입을 진행할 수 없습니다.');
        }
        // 본인확인 관련 세션 초기화
        session()->put("ss_cert_no", "");
        session()->put("ss_cert_hash", "");
        session()->put("ss_cert_type", "");

        $skin = cache("config.join")->skin ? : 'default';

        $params = [
            'agreeStipulation' => $request->agreeStipulation,
            'agreePrivacy' => $request->agreePrivacy,
        ];

        return viewDefault("user.$skin.register", $params);
    }

    // 회원 가입 수행
    public function register(Request $request)
    {
        if(!cache('config.sns')->googleRecaptchaClient || !cache('config.sns')->googleRecaptchaServer) {
            return alertRedirect('자동등록방지 키가 등록되지 않아서 회원가입을 진행할 수 없습니다.');
        }

        ReCaptcha::reCaptcha($request);
        $adminConfig = new Config();
        $rulePassword = $adminConfig->getPasswordRuleByConfigPolicy();
        $rules = array_add($this->userModel->rulesRegister, 'password', $rulePassword);
        $messages = $this->userModel->messages;

        // 수동 유효성 검사
        $validator = validator($request->all(), $rules, $messages);
        if($validator->fails()) {
            return redirect(route('user.register.form.get', $request->except(['password', 'password_confirmation', '_token', 'g-recaptcha-response'])))->withErrors($validator);
        }

        if(cache('config.cert')->certUse && cache('config.cert')->certReq) {
            if( trim($request->certNo) != session()->get('ss_cert_no') || !session()->get('ss_cert_no') ) {
                return alertErrorWithInput('회원가입을 위해서는 본인확인을 해주셔야 합니다.', 'hpCert');
            }
        }

        if(cache('config.cert')->certUse && session()->get('ss_cert_type') && session()->get('ss_cert_dupinfo')) {
            $cert = new Cert();
            $checkUser = $cert->checkExistDupInfo($request->email, session()->get('ss_cert_dupinfo'));
            if($checkUser) {
                return alertErrorWithInput("입력하신 본인확인 정보로 가입된 내역이 존재합니다.\\n회원이메일 : ". $checkUser->email);
            }
        }

        $user = $this->userModel->joinUser($request);
        if(!cache('config.email.default')->emailCertify) {
            Auth::login($user);
        }

        return redirect(route('user.welcome', [
            'nick' => $user->nick,
            'email' => $user->email,
        ]));
    }

    // 회원 가입 정보 입력 폼으로 리다이렉트 하는 메소드
    public function registerFormGet(Request $request)
    {
        $skin = cache("config.join")->skin ? : 'default';

        return viewDefault("user.$skin.register", $request->all());
    }

}
