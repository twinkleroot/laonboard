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

    public function __construct(Request $request)
    {
        $this->middleware('guest');
        $this->request = $request;
        $this->config = Config::getConfig('config.join');
    }

    public function redirectToProvider()
    {

        return Socialite::with('naver')->redirect();
    }

    // 소셜인증 후 데이터를 받아서 처리하는 메서드
    public function handleProviderCallback()
    {
        $userFromSocial = Socialite::with('naver')->user();

        $socialLogin = SocialLogin::where([
            'provider' => 'naver',
            'social_id' => $userFromSocial->getId(),
        ])->first();

        $userToLogin = null;
        if(!is_null($socialLogin)) {
            $userToLogin = $socialLogin->user()->first();
        }

        if(is_null($userToLogin)) {
            $userToLogin = new User([
                'name' => is_null($userFromSocial->getName()) ? $userFromSocial->getNickname() : $userFromSocial->getName(),
                'email' => $userFromSocial->getEmail(),
                'nick' => $userFromSocial->getNickname(),
                'nick_date' => Carbon::now()->toDateString(),
                'ip' => $this->request->ip(),
                'level' => $this->config->joinLevel,
                'point' => $this->config->joinPoint,
            ]);
            // 소셜을 통해 처음 로그인한 사용자의 정보를 User 테이블에 저장.
            $userToLogin->save();

            $user = User::find($userToLogin->id);
            $socialLogin = new SocialLogin([
                'provider' => 'naver',
                'social_id' => $userFromSocial->getId(),
                'social_token' => $userFromSocial->token,
            ]);

            // User 모델과 SocialLogin 모델의 연관관계를 이용해서 social_logins 테이블에 소셜 데이터 저장.
            $user->socialLogins()->save($socialLogin);
        }

        Auth::login($userToLogin);

        return redirect(route('home'));
    }
}
