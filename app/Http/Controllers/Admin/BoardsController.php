<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Board;
use App\Write;
use Session;

class BoardsController extends Controller
{
    public $boardModel;
    public $writeModel;

    public function __construct(Board $board, Write $write)
    {
        $this->middleware('level:10');

        $this->boardModel = $board;
        $this->writeModel = $write;
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
    public function create(Request $request)
    {
        $params = $this->boardModel->getBoardCreateParams($request);

        return view('admin.boards.form', $params);
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
            'table_name' => 'required|max:20|unique:boards|regex:/^[a-zA-Z0-9_]+$/',
            'group_id' => 'required',
            'subject' => 'required',
        ];

        $this->validate($request, $rule);

        $post = $this->writeModel->createWriteTable($request->get('table_name'));
        $board = $this->boardModel->createBoard($request->all());

        if(is_null($board) && $post) {
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
        $params = $this->boardModel->getBoardEditParams($id);

        return view('admin.boards.form', $params);
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
        $rule = [
            'group_id' => 'required',
            'subject' => 'required',
        ];

        $this->validate($request, $rule);

        $subject = $this->boardModel->updateBoard($request->all(), $id);

        if(!$subject) {
            abort('500', '게시판 설정의 변경에 실패하였습니다.');
        }

        return redirect(route('admin.boards.index'))->with('message', $subject . ' 게시판의 설정이 변경되었습니다.');
    }

    // 선택 수정 수행
    public function selectedUpdate(Request $request)
    {
        $this->boardModel->selectedUpdate($request);

        return redirect(route('admin.boards.index'))->with('message', '선택한 게시판 정보가 수정되었습니다.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request $request, int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $message = $this->boardModel->deleteBoards($request->get('ids'));

        return redirect(route('admin.boards.index'))->with('message', $message);
    }

    public function copyForm($id)
    {
        return view('admin.boards.copy')->with('board', Board::findOrFail($id));
    }

    public function copy(Request $request)
    {
        $rule = [
            'table_name' => 'required|max:20|unique:boards|regex:/^[a-zA-Z0-9_]+$/',
            'subject' => 'required',
        ];

        Session::put('table_name', $request->get('table_name'));

        $this->validate($request, $rule);

        $originalBoard = Board::findOrFail($request->get('id'));

        $board = $this->boardModel->copyBoard($request->all());
        $post = $this->writeModel->createWriteTable($request->get('table_name'));

        $message = $originalBoard->subject . ' 게시판이 복사되었습니다.';
        // 구조와 데이터를 함께 복사하는 경우
        if($request->get('copy_case') == 'schema_data_both') {  // Write instance를 새로 만들어야 해서 여기에 구현함.
            // 원본 테이블의 모델을 지정한다.
            $originalWrite = new Write();
            $originalWrite->setTableName($originalBoard->table_name);

            // 대상 테이블의 모델을 지정하고 데이터를 넣는다.
            $destinationWrite = new Write();
            $destinationWrite->setTableName($request->get('table_name'));
            if($destinationWrite->insert($originalWrite->get()->toArray())) {
                $message = $originalBoard->subject . ' 게시판과 데이터가 복사되었습니다.';
            } else {
                $message = $originalBoard->subject . ' 게시판과 데이터 복사에 실패하였습니다.';
            }
        }

        if(is_null($board) && $post) {
            abort('500', '게시판 생성에 실패하였습니다.');
        }

        return view('message', [
            'message' => $message,
            'reload' => 1,
            'popup' => 0,
            'redirect' => '/admin/boards/copy/'. $originalBoard->id,
        ]);
    }
}
