<?php

namespace App\Http\Controllers\Board;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Board;
use App\Write;
use App\Config;

class BoardController extends Controller
{

    public $boardModel;
    public $writeModel;

    public function __construct(Board $board, Write $write)
    {
        $this->boardModel = $board;
        $this->writeModel = $write;
    }
    /**
     * Display a listing of the resource.
     *
     * @param integer $boardId
     * @return \Illuminate\Http\Response
     */
    public function index($boardId, Request $request)
    {
        $kind = $request->has('kind') ? $request->get('kind') : '';
        $keyword = $request->has('keyword') ? $request->get('keyword') : '';

        $params = $this->boardModel->getBbsIndexParams($boardId, $kind, $keyword);

        return view('board.index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    // search
    public function search(Request $request)
    {
        $boardId = $request->segments()[1];
        $kind = $request->get('kind');
        $keyword = $request->get('keyword');

        return redirect('/board/' . $boardId . '/' . $kind . '/' . $keyword);
    }
}
