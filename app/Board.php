<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Group;
use App\Services\BoardSingleton;

class Board extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function __construct()
    {
        $this->table = 'boards';
    }

    // 게시판 그룹 모델과의 관계 설정
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public static function getBoard($boardName, $key='id')
    {
        return BoardSingleton::getInstance($boardName, $key);
    }

}
