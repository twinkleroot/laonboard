<?php

namespace App\Http\Controllers\Board;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Write;
use App\Board;
use App\Move;
use Exception;

class MoveController extends Controller
{
    public $writeModel;
    public $move;

    public function __construct(Request $request, Move $move, Write $write)
    {
        $this->writeModel = $write;
        $this->writeModel->board = Board::getBoard($request->boardName, 'table_name');
        $this->writeModel->setTableName($request->boardName);
        $this->move = $move;
    }

    // 게시물 복사 및 이동 폼
    public function move(Request $request, $boardName)
    {
        $params = $this->move->getMoveParams($boardName, $request);

        return view('board.move', $params);
    }

    // 게시물 복사 및 이동 수행
    public function moveUpdate(Request $request, $boardName)
    {
        $writeIds = session()->get('move_writeIds');
        // 복사 및 이동
        try {
            $this->move->copyWrites($this->writeModel, $writeIds, $request);
            if($request->type == 'move') {
                $this->move->moveWrites($this->writeModel, $writeIds, $request);
                return redirect(route('message'))->with([
                    'message' => '게시물 이동이 완료되었습니다.',
                    'openerRedirect' => route('board.index', $boardName),
                ]);
            } else {
                abort(200, '게시물 복사가 완료되었습니다.');
            }
        } catch (Exception $e) {
            return alertClose($e->getMessage());
        }

    }
}
