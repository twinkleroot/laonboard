<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Carbon\Carbon;

class SocialLogin extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'social_id', 'social_token',
    ];

    public function __construct()
    {
        $this->table = 'social_logins';
    }

    // SocialLogin 테이블과 Users 테이블과의 관계는 N:1
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function socialLoginCallback($userFromSocial, $provider)
    {
        // 연결된 소셜 로그인 정보가 있는지 확인
        $socialLogin = SocialLogin::where([
            'provider' => $provider,
            'social_id' => $userFromSocial->getId(),
        ])->first();

        if(is_null($socialLogin)) {
            // 소셜에서 받아온 데이터를 세션에 저장한다.
            session()->put('userFromSocial', $userFromSocial);
            return 'view';
        } else {
            // 연결된 소셜 정보에 해당하는 유저로 로그인
            $this->userToSocialLogin($socialLogin);
            return 'redirect';
        }
    }

    // 연결된 소셜 정보에 해당하는 유저로 로그인
    public function userToSocialLogin($socialLogin)
    {
        $userToLogin = $socialLogin->user()->first();
        Auth::login($userToLogin);
    }

    // 소셜 로그인 다음 단계를 위한 파라미터를 가져온다.
    public function getSocialParams($provider)
    {
        $userFromSocial = session()->get('userFromSocial');

        $existNick = User::where('nick', $userFromSocial->nickname)->first();
        $message = [
            'password' => '개인정보보호를 위해 비밀번호를 설정해주세요. 회원정보수정 시에도 사용됩니다.',
        ];

        if(!is_null($existNick)) {
            $message = array_add($message, 'nick', 'SNS에서 사용 중이신 닉네임을 이미 사용 중인 회원님이 있습니다. 다른 닉네임을 입력해 주세요.');
        }

        $existEmail = User::where('email', $userFromSocial->email)->first();
        if(!is_null($existEmail)) {
            $message = array_add($message, 'email', 'SNS에서 사용 중이신 이메일을 이미 사용 중인 회원님이 있습니다. 다른 이메일을 입력해 주세요.');
        }

        return [
            'userFromSocial' => $userFromSocial,
            'message' => $message,
            'provider' => $provider,
        ];
    }

    // 소셜로그인 정보 등록
    public function register($request, $user)
    {
        $socialLogin = $this->insertSocialLogins($request->ip(), $request->provider);

        // User 모델과 SocialLogin 모델의 관계를 이용해서 social_logins 테이블에 가입한 user_id와 소셜 데이터 저장.
        $user->socialLogins()->save($socialLogin);
    }

    public function insertSocialLogins($ip, $provider)
    {
        $userFromSocial = session()->get('userFromSocial');

        $id = SocialLogin::insertGetId([
            'provider' => $provider,
            'social_id' => $userFromSocial->id,
            'social_token' => $userFromSocial->token,
            'ip' => $ip,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return SocialLogin::find($id);
    }

}
