<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\SocialLogin;

class User extends Authenticatable
{
    use Notifiable;

    protected $dates = ['today_login', 'email_certify', 'nick_date', 'open_date', ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'nick', 'homepage',
        'level', 'sex', 'birth', 'tel', 'hp', 'certify',
        'adult', 'dupinfo', 'addr1', 'addr2',
        'addr_jibeon', 'signature', 'recommend', 'point',
        'login_ip', 'ip', 'email_certify', 'email_certify2',
        'memo', 'lost_certify', 'mailing', 'sms', 'open',
        'profile', 'memo_call', 'leave_date', 'intercept_date',
        'today_login', 'nick_date', 'open_date', 'zip',
    ];

    public static $rulesRegister = [
        'email' => 'required|email|max:255|unique:users',
        'password_confirmation' => 'required',
        'nick' => 'required|nick_length:2,4|unique:users|alpha_num',
    ];

    public static $rulesPassword = [
        'password_confirmation' => 'required',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function socialLogins()
    {
        return $this->hasMany(SocialLogin::class);
    }

    // 추천인 닉네임 구하기
    public static function recommendedPerson($user)
    {
        $recommendedNick = '';
        if(!is_null($user->recommend)) {
            $recommendedNick = User::where([
                'id' => $user->recommend,
            ])->first()->nick;
        }

        return $recommendedNick;
    }
}
