<?php

namespace App\Http\Controllers\Board;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Write;
use App\Move;
use Exception;

class MoveController extends Controller
{
    public $writeModel;
    public $move;

    public function __construct(Request $request, Move $move)
    {
        $this->writeModel = new Write($request->boardId);
        if( !is_null($this->writeModel->board) ) {
            $this->writeModel->setTableName($this->writeModel->board->table_name);
        }
        $this->move = $move;
    }

    // 게시물 복사 및 이동 폼
    public function move(Request $request, $boardId)
    {
        $params = $this->move->getMoveParams($boardId, $request);

        return view('board.move', $params);
    }

    // 게시물 복사 및 이동 수행
    public function moveUpdate(Request $request, $boardId)
    {
        $writeIds = session()->get('move_writeIds');
        // 복사 및 이동
        try {
            $this->move->copyWrites($this->writeModel, $writeIds, $request);
            if($request->type == 'move') {
                $this->move->moveWrites($this->writeModel, $writeIds, $request);
            }
        } catch (Exception $e) {
            return alertClose($e->getMessage());
        }

    }
}
