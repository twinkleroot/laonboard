<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class SocialLogin extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'provider', 'social_id', 'social_token', 'user_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'social_id', 'social_token',
    ];

    // SocialLogin 테이블과 Users 테이블과의 관계는 N:1
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
