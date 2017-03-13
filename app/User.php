<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'nick', 'homepage',
        'level', 'sex', 'birth', 'tel', 'hp', 'certify',
        'adult', 'dupinfo', 'addr1', 'addr2', 'addr3',
        'addr_jibeon', 'signature', 'recommend', 'point',
        'login_ip', 'ip', 'email_certify', 'email_certify2',
        'memo', 'lost_certify', 'mailing', 'sms', 'open',
        'profile', 'memo_call','leave_date', 'intercept_date',
        'today_login', 'nick_date', 'open_date',
    ];

    protected $dates = ['today_login', 'email_certify', ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
