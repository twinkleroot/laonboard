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

    public function getBoardManageIndexParams()
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
}
