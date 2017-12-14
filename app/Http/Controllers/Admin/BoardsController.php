<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Contracts\BoardInterface;
use App\Contracts\WriteInterface;

class BoardsController extends Controller
{
    public $boardModel;
    public $writeModel;

    public function __construct(BoardInterface $board, WriteInterface $write)
    {
        $this->boardModel = $board;
        $this->writeModel = $write;
    }
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (auth()->user()->cant('index', $this->boardModel)) {
            abort(403, '게시판 목록 보기에 대한 권한이 없습니다.');
        }

        $params = $this->boardModel->getBoardIndexParams($request);

        return view("admin.boards.index", $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }
        if (auth()->user()->cant('create', $this->boardModel)) {
            abort(403, '게시판 생성에 대한 권한이 없습니다.');
        }
        if(!\App\Models\Group::first()) {
            return alertRedirect('게시판그룹이 한개 이상 생성되어야 합니다.', route('admin.groups.create'));
        }

        $params = $this->boardModel->getBoardCreateParams($request);

        return view("admin.boards.form", $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        if (auth()->user()->cant('create', $this->boardModel)) {
            abort(403, '게시판 생성에 대한 권한이 없습니다.');
        }


        $rules = $this->rules();
        $rules = array_add($rules, 'table_name', 'required|max:20|unique:boards|regex:/^[a-zA-Z0-9_]+$/');

        $messages = $this->messages();
        $tableNameMessage = [
            'table_name.required' => '테이블을 입력해 주세요.',
            'table_name.max:20' => '테이블명은 20자리를 넘을 수 업습니다.',
            'table_name.unique:boards' => '이미 등록된 테이블명입니다.',
            'table_name.regex:/^[a-zA-Z0-9_]+$/' => '테이블명은 영문, 숫자, _만 입력 가능합니다.',
        ];
        $messages = array_collapse($messages, [$messages, $tableNameMessage]);

        $this->validate($request, $rules, $messages);

        $write = $this->boardModel->createWriteTable($request->table_name);
        $board = $this->boardModel->storeBoard($request->all());

        if(is_null($board) && $write) {
            abort(500, '게시판 생성에 실패하였습니다.');
        }

        return redirect(route('admin.boards.index'))->with('message', $board->subject . ' 게시판이 생성되었습니다.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string $boardName
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $boardName)
    {
        $board = $this->boardModel::where('table_name', $boardName)->first();

        if (!auth()->user()->isBoardAdmin($board) && auth()->user()->cant('update', $this->boardModel)) {
            abort(403, '게시판 수정에 대한 권한이 없습니다.');
        }

        $params = $this->boardModel->getBoardEditParams($request, $board->id);

        return view("admin.boards.form", $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $table_name
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        $board = $this->boardModel::find($id);
        if (!auth()->user()->isBoardAdmin($board) && auth()->user()->cant('update', $this->boardModel)) {
            abort(403, '게시판 수정에 대한 권한이 없습니다.');
        }

        $this->validate($request, $this->rules(), $this->messages());

        $subject = $this->boardModel->updateBoard($request->all(), $id);

        if(!$subject) {
            abort(500, '게시판 수정에 실패하였습니다.');
        }

        return redirect(route('admin.boards.edit', $board->table_name). $request->queryString)
            ->with('message', $subject . ' 게시판이 수정되었습니다.');
    }

    /**
     * selected writes update
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function selectedUpdate(Request $request)
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        $idArr = explode(',', $request->get('ids'));
        foreach($idArr as $id) {
            if (auth()->user()->cant('update', $this->boardModel->find($id))) {
                abort(403, '선택하신 게시판의 수정에 대한 권한이 없습니다.');
            }
        }

        $this->boardModel->selectedUpdate($request);

        return redirect(route('admin.boards.index'))->with('message', '선택한 게시판 정보가 수정되었습니다.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        if (auth()->user()->cant('delete', $this->boardModel)) {
            abort(403, '게시판 삭제에 대한 권한이 없습니다.');
        }

        $message = $this->boardModel->deleteBoards($request->get('ids'));

        return redirect(route('admin.boards.index'))->with('message', $message);
    }

    /**
     * view board copy form
     *
     * @param  string $boardName
     * @return \Illuminate\Http\Response
     */
    public function copyForm($boardName)
    {
        if (auth()->user()->cant('copy', $this->boardModel)) {
            abort(403, '게시판 복사에 대한 권한이 없습니다.');
        }

        return view("admin.boards.copy")->with('board', $this->boardModel::getBoard($boardName, 'table_name'));
    }

    /**
     * copy board
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function copy(Request $request)
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        if (auth()->user()->cant('copy', $this->boardModel)) {
            abort(403, '게시판 복사에 대한 권한이 없습니다.');
        }

        $rules = [
            'table_name' => 'required|max:20|unique:boards|regex:/^[a-zA-Z0-9_]+$/',
            'subject' => 'required',
        ];

        $messages = [
            'table_name.required' => '복사 테이블명을 입력해 주세요.',
            'table_name.max' => '복사 테이블명은 20글자 내로 입력해 주세요.',
            'table_name.unique' => '복사 테이블명에 입력한 테이블명이 이미 존재 합니다. 다른 테이블명을 입력해 주세요.',
            'table_name.regex' => '복사 테이블명엔 영문자, 숫자, 언더스코어(_)로 구성해서 입력해 주세요.',
            'subject.required' => '게시판 제목을 입력해 주세요.',
        ];

        session()->put('table_name', $request->table_name);

        $this->validate($request, $rules, $messages);

        $originalBoard = $this->boardModel->find($request->id);

        $board = $this->boardModel->copyBoard($request->all());
        // 게시판 테이블 생성
        $write = $this->boardModel->createWriteTable($request->table_name);

        $message = $originalBoard->subject . ' 게시판이 복사되었습니다.';
        // 구조와 데이터를 함께 복사하는 경우
        if($request->get('copy_case') == 'schema_data_both') {
            $writeToCopy = $this->writeModel;
            // 원본 테이블을 지정한다.
            $writeToCopy->setTableName($originalBoard->table_name);
            // 원본 테이블의 데이터를 가져온다.
            $orginalDatas = $writeToCopy->get()->toArray();
            foreach($orginalDatas as $key => $value) {
                $orginalDatas[$key] = array_except($value, ['isDelete', 'isEdit', 'isReply']);
            }

            // 대상 테이블을 지정하고 데이터를 넣는다.
            $writeToCopy->setTableName($board->table_name);
            if($writeToCopy->insert($orginalDatas)) {
                $message = $originalBoard->subject . ' 게시판과 데이터가 복사되었습니다.';
            } else {
                $message = $originalBoard->subject . ' 게시판과 데이터 복사에 실패하였습니다.';
            }
        }

        if(is_null($board) && $write) {
            abort(500, '게시판 복사에 실패하였습니다.');
        }

        return view('common.message', [
            'message' => $message,
            'reload' => 1,
            'popup' => 0,
            'redirect' => '/admin/boards/copy/'. $originalBoard->table_name,
        ]);
    }

    /**
     * delete this board's thumbnail image
     *
     * @param  Request $request
     * @param  string $boardName
     * @return \Illuminate\Http\Response
     */
    public function deleteThumbnail(Request $request, $boardName)
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        $params = $this->boardModel->deleteThumbnail($request->dir, $boardName);
        $queryString = $request->getQueryString();
        $params = array_add($params, 'queryString', $queryString);

        return view("admin.boards.thumbnail_delete", $params);
    }

    /**
     * view order list
     *
     * @param  Request $request
     * @param  string $boardName
     * @return \Illuminate\Http\Response
     */
    public function orderList(Request $request, $boardName)
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        $params = $this->boardModel->orderList($request, $boardName);

        return view("admin.boards.order_adjustment", $params);
    }

    /**
     * adjust write's order
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function adjustOrder(Request $request)
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        $message = $this->boardModel->adjustOrder($request);

        return redirect()->back()->with('message', $message);
    }

    // 유효성 검사 규칙
    public function rules()
    {
        return [
            'group_id' => 'bail|required',
            'subject' => 'bail|required',
            'count_delete' => 'bail|numeric|required',
            'count_modify' => 'bail|numeric|required',
            'read_point' => 'bail|numeric|required',
            'write_point' => 'bail|numeric|required',
            'comment_point' => 'bail|numeric|required',
            'download_point' => 'bail|numeric|required',
            'subject_len' => 'bail|numeric|required',
            'page_rows' => 'bail|numeric|required',
            'new' => 'bail|numeric|required',
            'hot' => 'bail|numeric|required',
            'image_width' => 'bail|numeric|required',
            'gallery_height' => 'bail|numeric|required',
            'upload_size' => 'bail|numeric|required',
            'upload_count' => 'bail|numeric|required',
            'order' => 'bail|numeric|nullable',
            'write_min' => 'bail|numeric|nullable',
            'write_max' => 'bail|numeric|nullable',
            'comment_min' => 'bail|numeric|nullable',
            'comment_max' => 'bail|numeric|nullable',
        ];
    }

    // 에러 메세지
    public function messages()
    {
        return [
            'group_id.required' => '그룹을 입력해 주세요.',
            'subject.required' => '게시판 제목을 입력해 주세요.',
            'count_delete.required' => '원글 삭제 불가를 입력해 주세요.',
            'count_modify.required' => '원글 수정 불가를 입력해 주세요.',
            'read_point.required' => '글읽기 포인트를 입력해 주세요.',
            'write_point.required' => '글쓰기 포인트를 입력해 주세요.',
            'comment_point.required' => '댓글쓰기 포인트를 입력해 주세요.',
            'download_point.required' => '다운로드 포인트를 입력해 주세요.',
            'subject_len.required' => '제목 길이를 입력해 주세요.',
            'page_rows.required' => '페이지당 목록 수를 입력해 주세요.',
            'new.required' => '새글 아이콘을 입력해 주세요.',
            'hot.required' => '인기글 아이콘을 입력해 주세요.',
            'image_width.required' => '이미지 폭 크기를 입력해 주세요.',
            'gallery_height.required' => '갤러리 이미지 높이를 입력해 주세요.',
            'upload_size.required' => '파일 업로드 용량을 입력해 주세요.',
            'upload_count.required' => '파일 업로드 개수를 입력해 주세요.',
            'count_delete.numeric' => '원글 삭제 불가 : 숫자가 아닙니다.',
            'count_modify.numeric' => '원글 수정 불가 : 숫자가 아닙니다.',
            'read_point.numeric' => '글읽기 포인트 : 숫자가 아닙니다.',
            'write_point.numeric' => '글쓰기 포인트 : 숫자가 아닙니다.',
            'comment_point.numeric' => '댓글쓰기 포인트 : 숫자가 아닙니다.',
            'download_point.numeric' => '다운로드 포인트 : 숫자가 아닙니다.',
            'subject_len.numeric' => '제목 길이 : 숫자가 아닙니다.',
            'page_rows.numeric' => '페이지당 목록 수 : 숫자가 아닙니다.',
            'new.numeric' => '새글 아이콘 : 숫자가 아닙니다.',
            'hot.numeric' => '인기글 아이콘 : 숫자가 아닙니다.',
            'image_width.numeric' => '이미지 폭 크기 : 숫자가 아닙니다.',
            'gallery_height.numeric' => '갤러리 이미지 높이 : 숫자가 아닙니다.',
            'upload_size.numeric' => '파일 업로드 용량 : 숫자가 아닙니다.',
            'upload_count.numeric' => '파일 업로드 개수 : 숫자가 아닙니다.',
            'order.numeric' => '출력 순서 : 숫자가 아닙니다.',
            'write_min.numeric' => '최소 글수 제한 : 숫자가 아닙니다.',
            'write_max.numeric' => '최대 글수 제한 : 숫자가 아닙니다.',
            'comment_min.numeric' => '최소 댓글수 제한 : 숫자가 아닙니다.',
            'comment_max.numeric' => '최대 댓글수 제한 : 숫자가 아닙니다.',
        ];
    }
}
