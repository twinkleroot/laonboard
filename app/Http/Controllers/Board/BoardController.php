<?php

namespace App\Http\Controllers\Board;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Board;
use App\Write;
use App\Config;
use App\BoardFile;
use App\BoardGood;
use App\User;
use Auth;
use Exception;
use Illuminate\Pagination\Paginator;

class BoardController extends Controller
{

    public $writeModel;
    public $boardModel;
    public $boardFileModel;
    public $boardGoodModel;

    public function __construct(Request $request, Board $board, BoardFile $boardFile, BoardGood $boardGood)
    {
        $this->writeModel = new Write($request->boardId);
        if( !is_null($this->writeModel->board) ) {
            $this->writeModel->setTableName($this->writeModel->board->table_name);
        }

        $this->boardModel = $board;
        $this->boardFileModel = $boardFile;
        $this->boardGoodModel = $boardGood;
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

    // 비밀글 일 때 비밀번호 물어보기
    public function checkPassword(Request $request, $boardId, $writeId)
    {
        return view('board.password', [
            'subject' => $this->writeModel->find($writeId)->subject,
            'boardId' => $boardId,
            'writeId' => $writeId,
        ]);
    }

    // 비밀번호 검사
    public function validatePassword(Request $request, $boardId)
    {
        $user = User::find($this->writeModel->find($request->writeId)->user_id);
        $email = $user->email;

        // 입력한 비밀번호와 작성자의 글 비밀번호를 비교한다.
        if(Auth::validate(['email' => $email, 'password' => $request->password])) {
            session()->put('secret_board_'.$boardId.'_write_'.$request->writeId, true);

            return redirect(session()->get('nextUri'));
         } else {
            return view('message', [
                'message' => '비밀번호가 틀립니다.',
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view(Request $request, $boardId, $writeId)
    {
        $params = $this->writeModel->getViewParams($request, $boardId, $writeId, $this->writeModel);

        if(isset($params['message'])) {
            return view('message', [
                'message' => $params['message']
            ]);
        }

        if($this->writeModel->board->use_list_view) {
            $params = array_collapse([$params, $this->writeModel->getBbsIndexParams($this->writeModel, $request)]);

            // $refer = explode('page=', $request->server('REQUEST_URI'));
            // $currentPage = 1;
            // if(count($refer) > 1 && !str_contains($refer[0], 'write')) {
            //     $currentPage = (int) $refer[1];
            // } else {
            //     $currentPage = $params['writes']->currentPage();
            // }
            // $lastPage = $params['writes']->lastPage();
            // $params['writes']->setCurrentPage($currentPage, $lastPage);
        } else {
            $params = array_collapse([$params, $this->writeModel->getPrevNextView($this->writeModel, $boardId, $writeId, $request)]);
        }

        return view('board.view', $params);
    }

    // 글 보기 중 링크 연결
    public function link($boardId, $writeId, $linkNo)
    {
        $result = $this->writeModel->beforeLink($this->writeModel, $writeId, $linkNo);

        if(isset($result['message'])) {
            return view('message', [
                'message' => $result['message']
            ]);
        }

        return view('board.link', [ 'linkUrl' => $result['linkUrl'] ]);
    }

    // 글 보기 중 첨부파일 다운로드
    public function download(Request $request, $boardId, $writeId, $fileNo)
    {
        $result = $this->writeModel->beforeDownload($request, $this->writeModel, $boardId, $writeId, $fileNo);

        if(isset($result['message'])) {
            return view('message', [
                'message' => $result['message']
            ]);
        }

        $file = $result;

        $filePath = $this->boardFileModel->getFilePath($file->file, $this->writeModel);

        return response()->download($filePath, $file->source);
    }

    // 글 보기 중 원본 이미지 보기
    public function viewImage($boardId, $writeId, $imageName)
    {
        $imagePath = $this->boardFileModel->getFilePath($imageName, $this->writeModel);
        $imageFileInfo = getimagesize($imagePath);

        return view('board.viewImage', [
            'imagePath' => $this->writeModel->board->table_name.'/'.$imageName,
            'width' => $imageFileInfo[0],
            'height' => $imageFileInfo[1],
        ]);
    }

    // 추천/비추천 ajax 메서드
    public function good($boardId, $writeId, $good)
    {
        $result = $this->boardGoodModel->good($this->writeModel, $writeId, $good);

        if(isset($result['error'])) {
            return [ 'error' => $result['error'] ];
        }

        return [ 'count' => $result['count'] ];
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
        if( !isset($request->subject)) {
            return view('message', [
                'message' => '제목을 입력해 주세요.'
            ]);
        }

        if( !isset($request->content)) {
            return view('message', [
                'message' => '내용을 입력해 주세요.'
            ]);
        }

        if( !$this->writeModel->checkWriteInterval() ) {
            return view('message', [
                'message' => '너무 빠른 시간내에 게시물을 연속해서 올릴 수 없습니다.'
            ]);
        }
        if( !$this->writeModel->checkIncorrectContent($request) ) {
            return view('message', [
                'message' => '내용에 올바르지 않은 코드가 다수 포함되어 있습니다.'
            ]);
        }
        if( !$this->writeModel->checkPostMaxSize($request) ) {
            return view('message', [
                'message' => '파일 또는 글내용의 크기가 서버에서 설정한 값을 넘어 오류가 발생하였습니다.\\npost_max_size='.ini_get('post_max_size').' , upload_max_filesize='.ini_get('upload_max_filesize').'\\n게시판관리자 또는 서버관리자에게 문의 바랍니다.',
            ]);
        }
        if( !$this->writeModel->checkAdminAboutNotice($request) ) {
            return view('message', [
                'message' => '파일 또는 글내용의 크기가 서버에서 설정한 값을 넘어 오류가 발생하였습니다.\\npost_max_size='.ini_get('post_max_size').' , upload_max_filesize='.ini_get('upload_max_filesize').'\\n게시판관리자 또는 서버관리자에게 문의 바랍니다.',
            ]);
        }

        $writeId = $this->writeModel->storeWrite($this->writeModel, $request);

        if(count($request->attach_file) > 0) {
            $message = $this->boardFileModel->storeBoardFile($request, $boardId, $writeId);
            if($message != '') {
                return view('message', [
                    'message' => $message,
                    'redirect' => route('board.index', $boardId),
                ]);
            }
        }

        return redirect(route('board.view', ['boardId' => $boardId, 'writeId' => $writeId] ));
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
        // 복사 및 이동
        $message = $this->writeModel->copyWrites($this->writeModel, $request);

        return view('message', [
            'message' => $message,
            'popup' => 1,
            'reload' => 1,
        ]);
    }

}
