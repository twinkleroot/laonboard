<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Point;
use App\Models\Config;

class UsersController extends Controller
{

    public $config;
    public $skin;
    public $rulesPassword;
    public $userModel;

    public function __construct(Config $config, User $userModel)
    {
        $this->config = cache("config.join") ? : json_decode(Config::where('name', 'config.join')->first()->vars);
        $this->skin = $this->config->skin ? : 'default';
        $this->userModel = $userModel;

        $adminConfig = new Config();
        $this->rulePassword = $adminConfig->getPasswordRuleByConfigPolicy();
    }

    // 회원 정보 수정 폼
    public function edit()
    {
        if(auth()->user()->isSuperAdmin() ) {
            return alertRedirect('관리자의 회원정보는 관리자 화면에서 수정해 주십시오.');
        }

        $params = $this->userModel->editParams();
        $skin = $this->skin;
        $theme = cache('config.theme')->name;

        return viewDefault("$theme.users.$skin.edit", $params);
    }

    // 회원 정보 수정 폼에 앞서 비밀번호 한번 더 확인하는 폼
    public function checkPassword(Request $request)
    {
        $user = auth()->user();
        $work = is_null($request->work) ? session()->get('work') : $request->work;
        $params = ['email' => $user->email, 'work' => $work];
        $skin = $this->skin;
        $theme = cache('config.theme')->name;

        if(is_null($user->password)) {
            // 최초 비밀번호 설정
            return viewDefault("$theme.users.$skin.set_password", $params);
        } else {
            return viewDefault("$theme.users.$skin.confirm_password", $params);
        }
    }

    // 비밀번호 비교
    public function confirmPassword(Request $request)
    {
        $rules = [
            'password' => 'required'
        ];
        $messages = [
            'password.required' => '비밀번호를 입력해 주세요.'
        ];

        $this->validate($request, $rules, $messages);

        $email = auth()->user()->email;
        $work = $request->work;	// 비밀번호 비교 후 맞으면 수행할 작업

        // 입력한 비밀번호와 인증된 사용자의 비밀번호를 비교한다.
        if(Auth::validate(['email' => $email, 'password' => $request->password ])) {
            return redirect(route('user.'. $work));
        } else {
            return redirect(route('user.checkPassword'). "?work=$work")->withMessage('비밀번호가 틀립니다.');
        }
    }

    // 최초 비밀번호 설정
    public function setPassword(Request $request)
    {
        $rules = array_add($this->userModel->rulesPassword, 'password', $this->rulePassword);
        $messages = $this->userModel->messages;
        $messages = $this->userModel->addPasswordMessages($messages);

        $this->validate($request, $rules, $messages);

        $this->userModel->setPassword($request);

        return redirect(route('user.edit'));
    }

    // 회원 정보 수정
    public function update(Request $request)
    {
        $params = $this->userModel->editParams();
        $skin = $this->skin;
        $user = auth()->user();
        $rules = [];
        $messages = $this->userModel->messages;
        // 비밀번호를 변경할 경우 validation에 password 조건을 추가한다.
        if($request->password) {
            $rules = array_add($this->userModel->rulesPassword, 'password', $this->rulePassword);
            $messages = $this->userModel->addPasswordMessages($messages);
        }
        // 이메일을 변경할 경우 validation에 email 조건을 추가한다.
        $email = getEmailAddress($request->email);
        $changeEmail = ($email != $user->email);
        if($changeEmail) {
            $rules = array_add($rules, 'email', 'required|email|max:255|unique:users');
        }
        if($this->config->name) {
            $rules = array_add($rules, 'name', 'alpha_dash|nullable');
        }
        if($this->config->homepage) {
            $rules = array_add($rules, 'homepage', 'regex:'. config('laon.URL_REGEX'). '|nullable');
        }
        if($this->config->tel) {
            $rules = array_add($rules, 'tel', 'regex:/^[0-9]{2,3}-[0-9]{3,4}-[0-9]{4}$/|nullable');
        }
        if($this->config->hp) {
            $rules = array_add($rules, 'hp', 'regex:/^[0-9]{2,3}-[0-9]{3,4}-[0-9]{4}$/|nullable');
        }
        if($this->config->addr) {
            $rules = array_add($rules, 'addr1', 'nullable');
            $rules = array_add($rules, 'addr2', 'nullable');
        }
        if($this->config->recommend) {
            $rules = array_add($rules, 'recommend', 'nick_length:2,4|nullable');
        }
        if($request->icon) {
            $request->merge([
                'iconName' => $request->icon->getClientOriginalName()
            ]);
            $rules = array_add($rules, 'iconName', 'regex:/\.(gif)$/i|nullable');
        }

        $this->validate($request, $rules, $messages);

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
        $theme = cache('config.theme')->name;

        return viewDefault("$theme.users.$skin.welcome", $params);
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
        $theme = cache('config.theme')->name;

        return viewDefault("$theme.users.$skin.change_email", $params);
    }

    // 메일인증 메일주소 변경 실행
    public function updateEmail(Request $request)
    {
        // 메일인증 메일주소 변경
        $result = $this->userModel->changeCertifyEmail($request);

        return alertRedirect('인증메일을 '. $result. ' 메일로 다시 보내드렸습니다.\\n\\잠시후 '. $result. ' 메일을 확인하여 주십시오.');
    }

    // 자기소개
    public function profile($id)
    {
        $skin = $this->skin;
        $theme = cache('config.theme')->name;
        $params = [];
        try {
            $params = $this->userModel->getProfileParams($id);
        } catch (Exception $e) {
            return alertClose($e->getMessage());
        }

        return viewDefault("$theme.users.$skin.profile", $params);
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
        $theme = cache('config.theme')->name;
        $params = [];
        try {
            $params = $this->userModel->getFormMailParams($request);
        } catch (Exception $e) {
            return alertClose($e->getMessage());
        }

        return viewDefault("$theme.users.$skin.formmail", $params);
    }

    // 메일 보내기 실행
    public function send(Request $request)
    {
        $rules = [
            'subject' => 'required',
            'content' => 'required',
        ];
        if(auth()->guest()) {
            $rules = array_add($rules, 'toUser', 'required|email|max:255');
            $rules = array_add($rules, 'name', 'required|alpha_dash|max:20');
            $rules = array_add($rules, 'email', 'required|email|max:255');
        }
        $messages = [
            'toUser.required' => '받는 분 이메일을 입력해 주세요.',
            'toUser.email' => '받는 분 이메일에 올바른 Email양식으로 입력해 주세요.',
            'toUser.max' => '받는 분 이메일은 :max자리를 넘길 수 없습니다.',
            'name.required' => '이름을 입력해 주세요.',
            'name.alpha_dash' => '이름에 영문자, 한글, 숫자, 대쉬(-), 언더스코어(_)만 입력해 주세요.',
            'name.max' => '이름은 :max자리를 넘길 수 없습니다.',
            'email.required' => '이메일을 입력해 주세요.',
            'email.email' => '이메일에 올바른 Email양식으로 입력해 주세요.',
            'email.max' => '이메일은 :max자리를 넘길 수 없습니다.',
            'subject.required' => '제목을 입력해 주세요.',
            'content.required' => '내용을 입력해 주세요.',
        ];

        $this->validate($request, $rules, $messages);

        try {
            $this->userModel->sendFormMail($request);
        } catch (Exception $e) {
            return alertClose($e->getMessage());
        }

        return alertClose('메일을 정상적으로 발송하였습니다.');
    }

    // ajax - 닉네임이 금지단어와 같은지 검사
    public function userFilter(Request $request)
    {
        $nick = $request->nick;

        $filterStrs = explode(',', trim(implode(',', cache("config.join")->banId)));
        $returnArr['nick'] = '';

        foreach($filterStrs as $str) {
            // 제목 필터링 (찾으면 중지)
            $pos = stripos($nick, $str);
            if ($pos !== false) {
                $returnArr['nick'] = $str;
                break;
            }

        }

        return $returnArr;
    }

}
