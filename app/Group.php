<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function __construct()
    {
        $this->table = 'groups';
    }

    // 회원 모델과의 관계 설정
    public function users()
    {
        return $this->belongsToMany(User::class, 'group_user')->withPivot('id', 'created_at');
    }

    // 게시판 모델과의 관계 설정
    public function boards()
    {
        return $this->hasMany(Board::class);
    }

}
