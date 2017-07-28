<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Socialite;
use App\SocialLogin;
use App\User;
use Auth;
use Cache;
use Laravel\Socialite\Two\InvalidStateException;
use GuzzleHttp\Exception\ClientException;
use Carbon\Carbon;

class SocialController extends Controller
{
    public $request;
    public $config;
    public $userModel;
    public $socialModel;

    public function __construct(Request $request, User $user, SocialLogin $social)
    {
        $this->request = $request;
        $this->userModel = $user;
        $this->socialModel = $social;
    }

    // 소셜 연결(provider에 해당 provider로 연결 요청)
    public function redirectToProvider($provider)
    {
        return Socialite::with($provider)->redirect();
    }

    // 소셜인증 후 데이터를 받아서 처리하는 콜백 메서드(config/services.php에서 지정)
    public function handleProviderCallback($provider)
    {
        try {
            $userFromSocial = Socialite::with($provider)->user();
        } catch (InvalidStateException $e) {
            return alert('잘못된 접근입니다.');
        } catch (ClientException $e) {
            return alert('Bad client credentials');
        }

        // 소셜 로그인
        if(auth()->guest()) {
            $result = $this->socialModel->socialLoginCallback($userFromSocial, $provider);

            if($result == 'view') {
                // 소셜 계정을 처음 사용해서 로그인 했을 경우 기존 계정과 연결/ 회원가입 화면으로 연결
                $params = $this->socialModel->getSocialParams($provider);
                $skin = cache("config.join")->skin ? : 'default';

                return viewDefault("user.$skin.social", $params);
            } else { // 소셜 계정으로 로그인
                return redirect(route('home'));
            }
        } else { // 회원 정보 수정에서 소셜 계정 연결
            $message = $this->userModel->connectSocialAccount($userFromSocial, $provider, $this->request);
            return view('message', [
                'message' => $message,
                'popup' => 1,
                'reload' => 1,
            ]);
        }
    }

    // 소셜 로그인 -> 회원가입
    public function socialUserJoin(Request $request)
    {
        // 존재하는 닉네임인지 검사
        if(!is_null(User::where('nick', $request->get('nick'))->first())) {
            return alert('이미 존재하는 닉네임입니다.');
        }
        // 존재하는 이메일인지 검사
        if(!is_null(User::where('email', $request->get('email'))->first())) {
            return alert('이미 존재하는 이메일입니다.');
        }

        // 회원가입
        $user = $this->userModel->joinUser($request);
        // 소셜로그인 정보 등록
        $this->socialModel->register($request, $user);
        // 가입한 유저 로그인
        Auth::login($user);

        return redirect(route('home'));
    }

    // 소셜 로그인 -> 기존 계정과 연결
    public function connectExistAccount(Request $request)
    {
        // 해당 이메일로 가입한 회원이 존재하는지 검사
        $user = User::where('email', $request->get('email'))->first();
        if(is_null($user)) {
            return alert('가입된 이메일이 아닙니다.');
        }
        // 입력한 비밀번호와 인증된 사용자의 비밀번호를 비교한다.
        if(Auth::attempt(['email' => $request->get('email'), 'password' => $request->get('password') ], false, false)) {
            // 소셜로그인 정보 등록
            $this->socialModel->register($request, $user);
            // 가입한 유저 로그인
            Auth::login($user);

            return redirect(route('home'));
        } else {
            return alert("비밀번호가 틀립니다.\\n비밀번호는 대소문자를 구분합니다.");
        }
    }

}
