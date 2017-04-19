<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Socialite;
use App\SocialLogin;
use App\User;
use Auth;
use Carbon\Carbon;
use App\Config;

class SocialController extends Controller
{
    public $request;
    public $config;
    public $userModel;
    public $socialModel;

    public function __construct(Request $request, User $user, SocialLogin $social)
    {
        $this->middleware('guest');

        $this->request = $request;
        $this->config = Config::getConfig('config.join');
        $this->userModel = $user;
        $this->socialModel = $social;
    }

    public function redirectToProvider($provider)
    {
        return Socialite::with($provider)->redirect();
    }

    // 소셜인증 후 데이터를 받아서 처리하는 메서드
    public function handleProviderCallback($provider)
    {
        $userFromSocial = Socialite::with($provider)->user();

        // 연결된 소셜 로그인 정보가 있는지 확인
        $socialLogin = SocialLogin::where([
            'provider' => $provider,
            'social_id' => $userFromSocial->getId(),
        ])->first();

        if(is_null($socialLogin)) {
            // 소셜에서 받아온 데이터를 세션에 저장한다.
            session()->put('userFromSocial', $userFromSocial);

            // 소셜 로그인 다음 단계를 위한 파라미터를 가져온다.
            $params = $this->socialModel->getSocialParams($provider);

            // 소셜 계정을 처음 사용해서 로그인 했을 경우 기존 계정과 연결/ 이 계정으로 사용 선택 화면으로 연결
            return view('auth.social', $params);
        } else {
            // 연결된 소셜 정보에 해당하는 유저로 로그인
            $userToLogin = $socialLogin->user()->first();
            Auth::login($userToLogin);
        }

        return redirect(route('home'));
    }

    // 소셜 로그인 -> 계속하기 -> 회원가입 선택
    public function socialUserJoin(Request $request)
    {
        // 존재하는 닉네임인지 검사
        if(!is_null(User::where('nick', $request->get('nick'))->first())) {
            return redirect(route('message'))->with('message', '이미 존재하는 닉네임입니다.');
        }

        // 존재하는 이메일인지 검사
        if(!is_null(User::where('email', $request->get('email'))->first())) {
            return redirect(route('message'))->with('message', '이미 존재하는 이메일입니다.');
        }

        // 회원가입
        $user = $this->userModel->userJoin($request, $this->config);

        // 소셜로그인 정보 등록
        $this->socialModel->register($request, $user);

        // 가입한 유저 로그인
        Auth::login($user);

        return redirect(route('home'));
    }

    // 소셜 로그인 -> 기존 계정과 연결 선택
    public function connectExistAccount(Request $request)
    {
        // 해당 이메일로 가입한 회원이 존재하는지 검사
        $user = User::where('email', $request->get('email'))->first();
        if(is_null($user)) {
            return redirect(route('message'))->with('message', '가입된 이메일이 아닙니다.');
        }

        // 입력한 비밀번호와 인증된 사용자의 비밀번호를 비교한다.
        if(Auth::attempt(['email' => $request->get('email'), 'password' => $request->get('password') ], false, false)) {
            // 소셜로그인 정보 등록
            $this->socialModel->register($request, $user);

            // 가입한 유저 로그인
            Auth::login($user);

            return redirect(route('home'));
        } else {
            return redirect(route('message'))->with('message', '비밀번호가 틀립니다.\n비밀번호는 대소문자를 구분합니다.');
        }
    }
}
