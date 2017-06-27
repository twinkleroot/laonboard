<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mail;
use Auth;
use Cache;
use Socialite;
use Carbon\Carbon;
use App\ReCaptcha;
use App\User;
use App\Point;
use App\Config;
use App\Notification;

class UserController extends Controller
{

    public $config;
    public $skin;
    public $rulePassword;
    public $userModel;

    public function __construct(Config $config, User $userModel)
    {
        $this->config = Cache::get("config.join");
        $this->skin = $this->config->skin;
        $this->rulePassword = Config::getRulePassword('config.join', $this->config);
        $this->userModel = $userModel;
    }

    // 회원 정보 수정 폼
    public function edit()
    {
        return view('user.'. $this->skin. '.edit', $this->userModel->editFormData($this->config));
    }

    // 회원 정보 수정 폼에 앞서 비밀번호 한번 더 확인하는 폼
    public function checkPassword(Request $request)
    {
        $user = auth()->user();
        if(is_null($user->password)) {
            return view('user.'. $this->skin. '.set_password');   // 최초 비밀번호 설정
        } else {
            return view('user.'. $this->skin. '.confirm_password', ['email' => $user->email, 'work' => $request->work]);
        }
    }

    // 비밀번호 비교
    public function confirmPassword(Request $request)
    {
        $user = auth()->user();
        $email = $user->email;
        $work = $request->work;

        // 입력한 비밀번호와 인증된 사용자의 비밀번호를 비교한다.
        if(Auth::validate(['email' => $email, 'password' => $request->get('password') ])) {
            return redirect(route('user.'. $work));
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
            $user = auth()->user();
            // 입력값 유효성 검사
            $rule = array_add($this->userModel->rulesPassword, 'password', $this->rulePassword);
            // 이메일을 변경할 경우 validation에 email 조건을 추가한다.
            $changeEmail = $request->get('email') != $user->email;
            if($changeEmail) {
                $rule = array_add($rule, 'email', 'required|email|max:255|unique:users');
            }

            $this->validate($request, $rule);

            $returnVal = $this->userModel->updateUserInfo($request, $this->config);

            if($returnVal == 'notExistRecommend') {
                return redirect(route('user.edit'))->withErrors(['recommend' => '추천인이 존재하지 않습니다.']);
            } else {
                if($changeEmail) {
                    Auth::logout();
                }
                return redirect('/home')->with('message', $user->nick . '님의 회원정보가 변경되었습니다.');
            }
        } else {
            return view('user.'. $this->skin. '.edit', $this->userModel->editFormData($this->config))
                ->withErrors(['reCaptcha' => '자동등록방지 입력이 틀렸습니다. 다시 입력해 주십시오.']);
        }
    }

    // 회원 가입 결과, 웰컴 페이지
    public function welcome(Request $request)
    {
        return view('user.'. $this->skin. '.welcome', [
            'nick' => $request->nick,
            'email' => $request->email,
        ]);
    }

    // 회원 정보 수정에서 소셜 연결 해제
    public function disconnectSocialAccount(Request $request)
    {
        return $this->userModel->disconnectSocialAccount($request);
    }

    // 메일인증 메일주소 변경 폼
    public function editEmail($email)
    {
        return view('user.'. $this->skin. '.change_email', [
            'email' => $email
        ]);
    }

    // 메일인증 메일주소 변경 실행
    public function updateEmail(Request $request)
    {
        if(ReCaptcha::reCaptcha($request)) {    // 구글 리캡챠 체크
            // 메일인증 메일주소 변경
            $result = $this->userModel->changeCertifyEmail($request);

            $message = '인증메일을 '. $result. ' 메일로 다시 보내드렸습니다.\\n\\잠시후 '. $result. ' 메일을 확인하여 주십시오.';
            return view('message', [
                'message' => $message,
                'redirect' => '/',
            ]);
        } else {
            return back()->withErrors(['reCaptcha' => '자동등록방지 입력이 틀렸습니다. 다시 입력해 주십시오.']);
        }
    }

    // 개인 별 포인트 목록
    public function pointList($id)
    {
        $point = new Point();
        $params = $point->getPointList($id);

        return view('user.'. $this->skin. '.point', $params);
    }

    // 자기소개
    public function profile($idHashkey)
    {
        $params = $this->userModel->getProfileParams($idHashkey);

        return view('user.'. $this->skin. '.profile', $params);
    }

    // 회원 탈퇴
    public function leave()
    {
        $message = $this->userModel->leaveUser();

        return view('message', [
            'message' => $message,
            'redirect' => route('home')
        ]);
    }
}
