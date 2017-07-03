<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Board;

class Group extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    // 회원 모델과의 관계 설정
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('id', 'created_at');
    }

    // 게시판 모델과의 관계 설정
    public function boards()
    {
        return $this->hasMany(Board::class);
    }

}
