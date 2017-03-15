<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
        'adult', 'dupinfo', 'addr1', 'addr2', 'addr3',
        'addr_jibeon', 'signature', 'recommend', 'point',
        'login_ip', 'ip', 'email_certify', 'email_certify2',
        'memo', 'lost_certify', 'mailing', 'sms', 'open',
        'profile', 'memo_call','leave_date', 'intercept_date',
        'today_login', 'nick_date', 'open_date', 'zip',
        // zip 아직 안넣음.
    ];

    public static $rules = [
        'name' => 'required|max:255',
        'email' => 'required|email|max:255|unique:users',
        'password' => 'required|min:6',
        'nick' => 'required|nick_length:2,4|unique:users|alpha_num',
        'tel' => 'nullable|numeric',
        'hp' => 'nullable|numeric',
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
