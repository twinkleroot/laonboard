<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    // index 페이지에서 필요한 파라미터 가져오기
    public function getBoardIndexParams()
    {
        $boards = Board::all();
        $accessible_groups = Group::where([
            'use_access' => 1,
        ])->get();

        return [
            'boards' => $boards,
            'accessible_groups' => $accessible_groups
        ];
    }

    // create 페이지에서 필요한 파라미터 가져오기
    public function getBoardCreateParams()
    {
        $accessible_groups = Group::where([
            'use_access' => 1,
        ])->get();

        return [
            'accessible_groups' => $accessible_groups,
        ];
    }

    // 게시판 생성
    public function createBoard($data)
    {
        $data = array_except($data, ['_token']);
        return Board::create($data);
    }
}
