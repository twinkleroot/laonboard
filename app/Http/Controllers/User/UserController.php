<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mail;
use Auth;
use App\Mail\EmailCertify;
use App\ReCaptcha;
use App\User;
use Carbon\Carbon;
use App\Config;

class UserController extends Controller
{

    public $config;
    public $rulePassword;
    public $userModel;

    public function __construct(Config $config, User $userModel)
    {
        $this->config = Config::getConfig('config.join');
        $this->rulePassword = Config::getRulePassword('config.join');
        $this->userModel = $userModel;
    }

    // 회원 정보 수정 폼
    public function edit()
    {
        return view('user.edit', $this->userModel->editFormData($this->config));
    }

    // 회원 정보 수정 폼에 앞서 비밀번호 한번 더 확인하는 폼
    public function checkPassword()
    {
        $user = Auth::user();
        if(is_null($user->password)) {
            return view('user.set_password');   // 최초 비밀번호 설정
        } else {
            return view('user.confirm_password')->with('email', $user->email);
        }
    }

    // 비밀번호 비교
    public function confirmPassword(Request $request)
    {
        $user = Auth::user();
        $email = $user->email;

        // 입력한 비밀번호와 인증된 사용자의 비밀번호를 비교한다.
        if(Auth::attempt(['email' => $email, 'password' => $request->get('password') ], false, false)) {
            return redirect(route('user.edit'));
        } else {
            return redirect(route('user.checkPassword'))->with('message', '비밀번호가 틀립니다.');
        }
    }

    // 최초 비밀번호 설정
    public function setPassword(Request $request)
    {
        $rule = array_add($this->userModel->rulesPassword, 'password', $this->rulePassword);
        $this->validate($request, $rule);

        $this->userModel->setPassword($request);

        return redirect(route('user.edit'));
    }

    // 회원 정보 수정
    public function update(Request $request)
    {
        if(ReCaptcha::reCaptcha($request)) {    // 구글 리캡챠 체크
            $user = Auth::user();
            // 입력값 유효성 검사
            $rule = array_add($this->userModel->rulesPassword, 'password', $this->rulePassword);
            $this->validate($request, $rule);

            $returnVal = $this->userModel->userInfoUpdate($request, $this->config);
            if($returnVal == 'notExistRecommend') {
                return redirect(route('user.edit'))->withErrors(['recommend' => '추천인이 존재하지 않습니다.']);
            } else {
                return redirect('/index')->with('message', $user->nick . '님의 회원정보가 변경되었습니다.');
            }
        } else {
            return view('user.edit', $this->userModel->editFormData($this->config))
                ->withErrors(['reCaptcha' => '자동등록방지 입력이 틀렸습니다. 다시 입력해 주십시오.']);
        }
    }

    // 회원 가입 결과, 웰컴 페이지
    public function welcome()
    {
        $user = Auth::user();

        // 인증 이메일 발송
        if($this->config->emailCertify == 1) {
            Mail::to(Auth::user())->send(new EmailCertify());
        }

        return view('user.welcome', [
            'emailCertify' => $this->config->emailCertify,
            'user' => $user,
        ]);
    }

}
