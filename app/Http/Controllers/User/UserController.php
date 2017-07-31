<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mail;
use Auth;
use Cache;
use Exception;
use Socialite;
use Carbon\Carbon;
use App\ReCaptcha;
use App\User;
use App\Point;
use App\Admin\Config;
use App\Notification;

class UserController extends Controller
{

    public $skin;
    public $rulePassword;
    public $userModel;

    public function __construct(Config $config, User $userModel)
    {
        $this->skin = cache("config.join")->skin ? : 'default';
        $this->userModel = $userModel;

        $adminConfig = new Config();
        $this->rulePassword = $adminConfig->getPasswordRuleByConfigPolicy();
    }

    // 회원 정보 수정 폼
    public function edit()
    {
        if(session()->get('admin')) {
            return alertRedirect('관리자의 회원정보는 관리자 화면에서 수정해 주십시오.');
        }

        // 본인확인 관련 세션 초기화
        session()->put("ss_cert_no", "");
        session()->put("ss_cert_hash", "");
        session()->put("ss_cert_type", "");

        $params = $this->userModel->editParams();
        $skin = $this->skin;

        return viewDefault("user.$skin.edit", $params);
    }

    // 회원 정보 수정 폼에 앞서 비밀번호 한번 더 확인하는 폼
    public function checkPassword(Request $request)
    {
        $user = auth()->user();
        $work = is_null($request->work) ? session()->get('work') : $request->work;
        $params = ['email' => $user->email, 'work' => $work];
        $skin = $this->skin;

        if(is_null($user->password)) {
            // 최초 비밀번호 설정
            return viewDefault("user.$skin.set_password");
        } else {
            return viewDefault("user.$skin.confirm_password", $params);
        }
    }

    // 비밀번호 비교
    public function confirmPassword(Request $request)
    {
        $email = auth()->user()->email;
        $work = $request->work;	// 비밀번호 비교 후 맞으면 수행할 작업

        // 입력한 비밀번호와 인증된 사용자의 비밀번호를 비교한다.
        if(Auth::validate(['email' => $email, 'password' => $request->password ])) {
            return redirect(route('user.'. $work));
        } else {
            return redirect(route('user.checkPassword'))->with('message', '비밀번호가 틀립니다.')->with('work', $work);
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
        ReCaptcha::reCaptcha($request);
        $params = $this->userModel->editParams();
        $skin = $this->skin;
        $user = auth()->user();
        $rule = [];
        // 비밀번호를 변경할 경우 validation에 password 조건을 추가한다.
        if($request->password && !Auth::validate(['email' => $user->email, 'password' => $request->password ])) {
            $rule = array_add($this->userModel->rulesPassword, 'password', $this->rulePassword);
        }
        // 이메일을 변경할 경우 validation에 email 조건을 추가한다.
        $email = getEmailAddress($request->email);
        $changeEmail = ($email != $user->email);
        if($changeEmail) {
            $rule = array_add($rule, 'email', 'required|email|max:255|unique:users');
        }

        $this->validate($request, $rule);

        $this->userModel->updateUserInfo($request);

        if($changeEmail) {
            Auth::logout();
        }

        return redirect(route('user.edit'));
    }

    // 회원 가입 결과, 웰컴 페이지
    public function welcome(Request $request)
    {
        $params = ['nick' => $request->nick, 'email' => $request->email,];
        $skin = $this->skin;

        return viewDefault("user.$skin.welcome", $params);
    }

    // 회원 정보 수정에서 소셜 연결 해제
    public function disconnectSocialAccount(Request $request)
    {
        return $this->userModel->disconnectSocialAccount($request);
    }

    // 메일인증 메일주소 변경 폼
    public function editEmail($email)
    {
        $params = ['email' => $email];
        $skin = $this->skin;

        return viewDefault("user.$skin.change_email", $params);
    }

    // 메일인증 메일주소 변경 실행
    public function updateEmail(Request $request)
    {
        ReCaptcha::reCaptcha($request);
        // 메일인증 메일주소 변경
        $result = $this->userModel->changeCertifyEmail($request);

        return alertRedirect('인증메일을 '. $result. ' 메일로 다시 보내드렸습니다.\\n\\잠시후 '. $result. ' 메일을 확인하여 주십시오.');
    }

    // 개인 별 포인트 목록
    public function pointList($id)
    {
        $point = new Point();
        $skin = $this->skin;
        $params = $point->getPointList($id);

        return viewDefault("user.$skin.point", $params);
    }

    // 자기소개
    public function profile($id)
    {
        $skin = $this->skin;
        $params = [];
        try {
            $params = $this->userModel->getProfileParams($id);
        } catch (Exception $e) {
            return alertClose($e->getMessage());
        }

        return viewDefault("user.$skin.profile", $params);
    }

    // 회원 탈퇴
    public function leave()
    {
        $message = $this->userModel->leaveUser();

        return alertRedirect($message);
    }

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

    // 툴팁 : 메일 보내기 양식
    public function form(Request $request)
    {
        $skin = $this->skin;
        $params = [];
        try {
            $params = $this->userModel->getFormMailParams($request);
        } catch (Exception $e) {
            return alertClose($e->getMessage());
        }

        return viewDefault("user.$skin.formmail", $params);
    }

    // 메일 보내기 실행
    public function send(Request $request)
    {
        try {
            $this->userModel->sendFormMail($request);
        } catch (Exception $e) {
            return alertClose($e->getMessage());
        }

        return alertClose('메일을 정상적으로 발송하였습니다.');
    }

    // ajax form validation
    public function existData(Request $request)
    {
        // 해당 키와 값에 해당하는 사용자가 있는지 검사
        if(User::where($request->key, $request->value)->first()) {
            return ['result' => true];
        }
        return ['result' => false];
    }

}
