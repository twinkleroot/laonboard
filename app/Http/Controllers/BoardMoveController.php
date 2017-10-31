<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\BoardInterface;
use App\Contracts\WriteInterface;
use App\Models\BoardMove;
use Exception;

class BoardMoveController extends Controller
{
    public $writeModel;
    public $move;

    public function __construct(Request $request, BoardMove $move, WriteInterface $write, BoardInterface $board)
    {
        $this->writeModel = $write;
        $this->writeModel->board = $board->getBoard($request->boardName, 'table_name');
        $this->writeModel->setTableName($request->boardName);
        $this->move = $move;
    }

    // 게시물 복사 및 이동 폼
    public function move(Request $request, $boardName)
    {
        $params = $this->move->getMoveParams($boardName, $request);

        $theme = cache('config.theme')->name ? : 'default';

        return viewDefault("$theme.boards.move", $params);
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
