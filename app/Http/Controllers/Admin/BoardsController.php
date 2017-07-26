<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Admin\Board;
use App\Write;
use App\Common\Util;
use Illuminate\Auth\Access\AuthorizationException;

class BoardsController extends Controller
{
    public $boardModel;

    public function __construct(Board $board)
    {
        $this->boardModel = $board;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (auth()->user()->cant('index', $this->boardModel)) {
            abort(403, '게시판 목록 보기에 대한 권한이 없습니다.');
        }

        $params = $this->boardModel->getBoardIndexParams($request);

		// $boards = $params['boards'];
		// $json = $boards->appends($request->except('page'))->jsonSerialize();
		// dd($boards->appends($request->except('page'))->url($boards->currentPage()));

        return view('admin.boards.index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (auth()->user()->cant('create', Board::class)) {
            abort(403, '게시판 생성에 대한 권한이 없습니다.');
        }

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
        if (auth()->user()->cant('create', Board::class)) {
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

        $this->validate($request, $this->rules(), $this->messages());

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if (auth()->user()->cant('update', $this->boardModel)) {
            abort(403, '게시판 수정에 대한 권한이 없습니다.');
        }

        $params = $this->boardModel->getBoardEditParams($request, $id);

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
        if (auth()->user()->cant('update', $this->boardModel)) {
            abort(403, '게시판 수정에 대한 권한이 없습니다.');
        }

        $this->validate($request, $this->rules(), $this->messages());

        $subject = $this->boardModel->updateBoard($request->all(), $id);

        if(!$subject) {
            abort(500, '게시판 설정의 수정에 실패하였습니다.');
        }

        return redirect(route('admin.boards.edit', $id). $request->queryString)
            ->with('message', $subject . ' 게시판의 설정이 수정되었습니다.');
    }

    // 선택 수정 수행
    public function selectedUpdate(Request $request)
    {
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
     * @param  Request $request, int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (auth()->user()->cant('delete', $this->boardModel)) {
            abort(403, '게시판 삭제에 대한 권한이 없습니다.');
        }

        $message = $this->boardModel->deleteBoards($request->get('ids'));

        return redirect(route('admin.boards.index'))->with('message', $message);
    }

    public function copyForm($id)
    {
        if (auth()->user()->cant('copy', Board::class)) {
            abort(403, '게시판 복사에 대한 권한이 없습니다.');
        }

        return view('admin.boards.copy')->with('board', Board::findOrFail($id));
    }

    public function copy(Request $request)
    {
        if (auth()->user()->cant('copy', Board::class)) {
            abort(403, '게시판 복사에 대한 권한이 없습니다.');
        }

        $rule = [
            'table_name' => 'required|max:20|unique:boards|regex:/^[a-zA-Z0-9_]+$/',
            'subject' => 'required',
        ];

        session()->put('table_name', $request->table_name);

        $this->validate($request, $rule);

        $originalBoard = Board::findOrFail($request->id);

        $board = $this->boardModel->copyBoard($request->all());
        // 게시판 테이블 생성
        $write = $this->boardModel->createWriteTable($request->table_name);

        $message = $originalBoard->subject . ' 게시판이 복사되었습니다.';
        // 구조와 데이터를 함께 복사하는 경우
        if($request->get('copy_case') == 'schema_data_both') {  // Write instance를 새로 만들어야 해서 여기에 구현함.
            // 원본 테이블의 모델을 지정한다.
            $originalWrite = new Write($originalBoard->id);
            $originalWrite->setTableName($originalBoard->table_name);

            // 대상 테이블의 모델을 지정하고 데이터를 넣는다.
            $destinationWrite = new Write($board->id);
            $destinationWrite->setTableName($board->table_name);
            if($destinationWrite->insert($originalWrite->get()->toArray())) {
                $message = $originalBoard->subject . ' 게시판과 데이터가 복사되었습니다.';
            } else {
                $message = $originalBoard->subject . ' 게시판과 데이터 복사에 실패하였습니다.';
            }
        }

        if(is_null($board) && $write) {
            abort(500, '게시판 복사에 실패하였습니다.');
        }

        return view('message', [
            'message' => $message,
            'reload' => 1,
            'popup' => 0,
            'redirect' => '/admin/boards/copy/'. $originalBoard->id,
        ]);
    }

    public function deleteThumbnail(Request $request, $id)
    {
        $params = $this->boardModel->deleteThumbnail($request->dir, $id);
        $queryString = $request->getQueryString();
        $params = array_add($params, 'queryString', $queryString);

        return view('admin.boards.thumbnail_delete', $params);
    }

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
            // 'table_width' => 'bail|numeric|required',
            'subject_len' => 'bail|numeric|required',
            'page_rows' => 'bail|numeric|required',
            'new' => 'bail|numeric|required',
            'hot' => 'bail|numeric|required',
            'image_width' => 'bail|numeric|required',
            // 'gallery_cols' => 'bail|numeric|required',
            // 'gallery_width' => 'bail|numeric|required',
            'gallery_height' => 'bail|numeric|required',
            'upload_size' => 'bail|numeric|required',
            'upload_count' => 'bail|numeric|required',
            'order' => 'bail|numeric|nullable',
            'write_min' => 'bail|numeric|nullable',
            'write_max' => 'bail|numeric|nullable',
            'comment_min' => 'bail|numeric|nullable',
            'comment_max' => 'bail|numeric|nullable',
            // 'mobile_subject_len' => 'bail|numeric|required',
            // 'mobile_page_rows' => 'bail|numeric|required',
            // 'mobile_gallery_width' => 'bail|numeric|required',
            // 'mobile_gallery_height' => 'bail|numeric|required',
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
            // 'table_width.required' => '게시판 폭을 입력해 주세요.',
            'subject_len.required' => '제목 길이를 입력해 주세요.',
            'page_rows.required' => '페이지당 목록 수를 입력해 주세요.',
            'new.required' => '새글 아이콘을 입력해 주세요.',
            'hot.required' => '인기글 아이콘을 입력해 주세요.',
            'image_width.required' => '이미지 폭 크기를 입력해 주세요.',
            // 'gallery_cols.required' => '갤러리 이미지 수를 입력해 주세요.',
            // 'gallery_width.required' => '갤러리 이미지 폭을 입력해 주세요.',
            'gallery_height.required' => '갤러리 이미지 높이를 입력해 주세요.',
            'upload_size.required' => '파일 업로드 용량을 입력해 주세요.',
            'upload_count.required' => '파일 업로드 개수를 입력해 주세요.',

            'count_delete.numeric' => '원글 삭제 불가 : 숫자가 아닙니다.',
            'count_modify.numeric' => '원글 수정 불가 : 숫자가 아닙니다.',
            'read_point.numeric' => '글읽기 포인트 : 숫자가 아닙니다.',
            'write_point.numeric' => '글쓰기 포인트 : 숫자가 아닙니다.',
            'comment_point.numeric' => '댓글쓰기 포인트 : 숫자가 아닙니다.',
            'download_point.numeric' => '다운로드 포인트 : 숫자가 아닙니다.',
            // 'table_width.numeric' => '게시판 폭 : 숫자가 아닙니다.',
            'subject_len.numeric' => '제목 길이 : 숫자가 아닙니다.',
            'page_rows.numeric' => '페이지당 목록 수 : 숫자가 아닙니다.',
            'new.numeric' => '새글 아이콘 : 숫자가 아닙니다.',
            'hot.numeric' => '인기글 아이콘 : 숫자가 아닙니다.',
            'image_width.numeric' => '이미지 폭 크기 : 숫자가 아닙니다.',
            // 'gallery_cols.numeric' => '갤러리 이미지 수 : 숫자가 아닙니다.',
            // 'gallery_width.numeric' => '갤러리 이미지 폭 : 숫자가 아닙니다.',
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
