<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Group;

class Board extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    // 게시판 그룹 모델과의 관계 설정
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public $board;
    
    public static function getBoard()
    {
        return $this->board;
    }

    private function setBoard($boardId)
    {
        $this->board = Board::find($boardId);
    }
}
