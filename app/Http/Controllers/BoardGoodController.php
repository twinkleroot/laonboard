<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\BoardInterface;
use App\Contracts\WriteInterface;
use App\Models\BoardGood;

class BoardGoodController extends Controller
{
    public $writeModel;
    public $boardGoodModel;

    public function __construct(Request $request, WriteInterface $write, BoardInterface $board, BoardGood $boardGood)
    {
        $this->writeModel = $write;
        $this->writeModel->board = $board->getBoard($request->boardName, 'table_name');
        $this->writeModel->setTableName($request->boardName);
        $this->boardGoodModel = $boardGood;
    }


    // 추천/비추천 ajax 메서드
    public function good($boardName, $writeId, $good)
    {
        $result = $this->boardGoodModel->good($this->writeModel, $writeId, $good);

        if(isset($result['error'])) {
            return [ 'error' => $result['error'] ];
        }

        return [ 'count' => $result['count'] ];
    }

}
