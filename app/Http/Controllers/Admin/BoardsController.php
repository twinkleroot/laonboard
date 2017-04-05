<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Board;

class BoardsController extends Controller
{
    public $boardModel;

    public function __construct(Board $board)
    {
        $this->middleware('level:10');

        $this->boardModel = $board;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $params = $this->boardModel->getBoardIndexParams();

        return view('admin.boards.index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = $this->boardModel->getBoardCreateParams();

        return view('admin.boards.create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rule = [
            // 'email' => 'required|email|max:255|unique:users',
            // 'nick' => 'required|nick_length:2,4|unique:users|alpha_num',
            // 'password' => $this->rulePassword[0] . '|' . $this->rulePassword[2],
        ];

        // $this->validate($request, $rule);

        $board = $this->boardModel->createBoard($request->all());
        if(is_null($board)) {
            abort('500', '게시판 생성에 실패하였습니다.');
        }

        return redirect(route('admin.boards.index'))->with('message', $board->subject . ' 게시판이 생성되었습니다.');
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
}
