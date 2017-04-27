<?php

namespace App\Http\Controllers\Board;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Board;
use App\Write;
use App\Config;
use App\BoardFile;
use Exception;

class BoardController extends Controller
{

    public $writeModel;
    public $boardModel;
    public $boardFileModel;

    public function __construct(Request $request, Board $board, BoardFile $boardFileModel)
    {
        $this->writeModel = new Write($request->boardId);
        if( !is_null($this->writeModel->board) ) {
            $this->writeModel->setTableName($this->writeModel->board->table_name);
        }

        $this->boardModel = $board;
        $this->boardFileModel = $boardFileModel;
    }
    /**
     * Display a listing of the resource.
     *
     * @param integer $boardId
     * @return \Illuminate\Http\Response
     */
    public function index($boardId, Request $request)
    {
        $params = $this->writeModel->getBbsIndexParams($this->writeModel, $request);

        if(isset($params['message'])) {
            return view('message', $params);
        }

        return view('board.index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($boardId)
    {
        $params = $this->writeModel->getBbsCreateParams($this->writeModel);

        return view('board.form', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $boardId)
    {
        if( !$this->writeModel->checkWriteInterval() ) {
            return view('message', [
                'message' => '너무 빠른 시간내에 게시물을 연속해서 올릴 수 없습니다.'
            ]);
        }
        $lastInsertId = $this->writeModel->storeWrite($this->writeModel, $request);

        if(count($request->attach_file) > 0) {
            $this->boardFileModel->storeBoardFile($request, $boardId, $lastInsertId);
        }
        return redirect(route('board.index', $boardId));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($boardId)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($boardId)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $boardId)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($boardId, string $writeId, Request $request)
    {
        $message = $this->writeModel->selectDeleteWrites($this->writeModel, $writeId);

        $returnUrl = $request->page == 1
                    ? route('board.index', $boardId)
                    : '/board/' . $boardId . '?page=' . $request->page ;
                    
        return redirect($returnUrl);
    }

    // 게시물 복사 및 이동 폼
    public function move($boardId, Request $request)
    {
        $params = $this->boardModel->getMoveParams($boardId, $request);

        return view('board.move', $params);
    }

    // 게시물 복사 및 이동 수행
    public function moveUpdate($boardId, Request $request)
    {
        // 복사
        $message = $this->writeModel->copyWrites($this->writeModel, $request);

        // 이동 == 복사 + 삭제
        if($request->type == 'move') {
            // 원래 있던 곳의 테이블에서 해당 게시물 삭제
            $message = $this->writeModel->deleteWrites($this->writeModel);
        }

        return view('message', [
            'message' => $message,
            'popup' => 1,
            'reload' => 1,
        ]);
    }

}
