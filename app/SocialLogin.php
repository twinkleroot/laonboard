<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

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

    // SocialLogin 테이블과 Users 테이블과의 관계는 N:1
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
