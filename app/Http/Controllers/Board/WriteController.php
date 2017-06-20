<?php

namespace App\Http\Controllers\Board;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Write;
use App\Point;
use App\Config;
use App\Board;
use App\BoardFile;
use App\BoardGood;
use App\User;
use App\Comment;
use App\Notification;
use Auth;
use Cache;
use Exception;
use Illuminate\Pagination\Paginator;

class WriteController extends Controller
{

    public $writeModel;
    public $boardFileModel;
    public $boardGoodModel;
    public $notification;

    public function __construct(Request $request, BoardFile $boardFile, BoardGood $boardGood, Comment $comment, Notification $notification)
    {
        $this->writeModel = new Write($request->boardId);
        if( !is_null($this->writeModel->board) ) {
            $this->writeModel->setTableName($this->writeModel->board->table_name);
        }

        $this->boardFileModel = $boardFile;
        $this->boardGoodModel = $boardGood;
        $this->notification = $notification;
    }
    /**
     * Display a listing of the resource.
     *
     * @param integer $boardId
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $boardId)
    {
        $params = $this->writeModel->getIndexParams($this->writeModel, $request);

        if(isset($params['message'])) {
            return view('message', $params);
        }

        $params = array_add($params, 'mainTheme', 'dafault');

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
        // 글 보기 데이터
        $params = $this->writeModel->getViewParams($request, $boardId, $writeId, $this->writeModel);

        if(isset($params['message'])) {
            return view('message', [
                'message' => $params['message']
            ]);
        }

        // 댓글 데이터
        $comment = new Comment();
        $params = array_collapse([$params, $comment->getCommentsParams($this->writeModel, $boardId, $writeId)]);

        // 전체 목록 보기 선택시 목록 데이터
        if($this->writeModel->board->use_list_view) {
            $params = array_collapse([$params, $this->writeModel->getIndexParams($this->writeModel, $request)]);

            // $refer = explode('page=', $request->server('REQUEST_URI'));
            // $currentPage = 1;
            // if(count($refer) > 1 && !str_contains($refer[0], 'write')) {
            //     $currentPage = (int) $refer[1];
            // } else {
            //     $currentPage = $params['writes']->currentPage();
            // }
            // $lastPage = $params['writes']->lastPage();
            // $params['writes']->setCurrentPage($currentPage, $lastPage);
        }
        // 이전글, 다음글 데이터 추가
        $params = array_collapse([$params, $this->writeModel->getPrevNextView($this->writeModel, $boardId, $writeId, $request)]);

        // 요청 URI 추가
        $params = array_add($params, 'requestUri', $request->getRequestUri());

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
        $params = $this->writeModel->getCreateParams($this->writeModel);

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
        $result = $this->writeModel->storeWrite($this->writeModel, $request);

        $writeId = 0;
        if(isset($result['message']) && !preg_match("/^[A-Z]+$/", $result['message'])) {
            return view('message', [
                    'message' => $result['message'],
                    'redirect' => route('board.view', ['boardId' => $boardId, 'writeId' => $request->writeId]),
                ]);
        } else {
            $writeId = $result;
        }

        if(count($request->attach_file) > 0) {
            $message = $this->boardFileModel->createBoardFiles($request, $boardId, $writeId);
            if($message != '') {
                return view('message', [
                    'message' => $message,
                    'redirect' => route('board.index', $boardId),
                ]);
            }
        }

        if(Cache::get('config.email.default')->emailUse && $this->writeModel->board->use_email) {
            $this->notification->sendWriteNotification($this->writeModel, $writeId);
        }

        return redirect(route('board.view', ['boardId' => $boardId, 'writeId' => $writeId] ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($boardId, $writeId)
    {
        $params = $this->writeModel->getEditParams($boardId, $writeId, $this->writeModel);

        return view('board.form', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $boardId, $writeId)
    {
        $file = 0;
        if(count($request->file_del) > 0 || count($request->attach_file) > 0) {
            // 첨부 파일 변경
            $result = $this->boardFileModel->updateBoardFiles($request, $boardId, $writeId);

            if(isset($result['message'])) {
                return view('message', [
                    'message' => $result['message'],
                    'redirect' => route('board.index', $boardId),
                ]);
            } else {
                $file = $result['fileCount'];
            }
        }
        // 게시 글 수정
        $this->writeModel->updateWrite($this->writeModel, $request, $writeId, $file);

        return redirect(route('board.view', ['boardId' => $boardId, 'writeId' => $writeId] ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createReply($boardId, $writeId)
    {
        $params = $this->writeModel->getReplyParams($boardId, $writeId, $this->writeModel);

        return view('board.form', $params);
    }

    /**
     * 글보기 - 삭제
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $boardId, $writeId)
    {
        $message = $redirect = '';
        $board = Board::find($boardId);

        if( $this->writeModel->hasReply($this->writeModel, $writeId) ) {
            $message = '이 글과 관련된 답변글이 존재하므로 삭제 할 수 없습니다.\\n\\n우선 답변글부터 삭제하여 주십시오.';
        } else if( $this->writeModel->hasComment($this->writeModel, $writeId)) {
            $message = '이 글과 관련된 코멘트가 존재하므로 삭제 할 수 없습니다.\\n\\n코멘트가 '. $board->count_delete. '건 이상 달린 원글은 삭제할 수 없습니다.';
        } else {
            $message = $this->deleteWriteCascade($boardId, $writeId);
            $redirect = route('board.index', $boardId);
        }

        if($message != '') {
            return view('message', [
                'message' => $message,
                'redirect' => $redirect,
            ]);
        }

        $returnUrl = route('board.index', $boardId). ($request->page == 1 ? '' : '?page='. $request->page);
        return redirect($returnUrl);
    }

    // 게시글 삭제하면서 게시글에 종속된 것들도 함께 삭제
    private function deleteWriteCascade($boardId, $writeId)
    {
        $message = '';
        // 부여되었던 포인트 삭제 및 조정 반영
        $point = new Point();
        $delPointResult = $point->deleteWritePoint($this->writeModel, $boardId, $writeId);
        if($delPointResult <= 0) {
            $message .= '정상적으로 게시글을 삭제하는데 실패하였습니다.(포인트 삭제)';
        }
        // 서버에서 파일 삭제, 썸네일 삭제, 에디터 첨부 이미지 파일, 썸네일 삭제, 파일 테이블 삭제
        $delFileResult = $this->boardFileModel->deleteWriteAndAttachFile($boardId, $writeId);
        if( array_search(false, $delFileResult) === false ) {
            $message .= '정상적으로 게시글을 삭제하는데 실패하였습니다.(첨부 파일 삭제)';
        }
        // 게시글 삭제
        $delWriteResult = $this->writeModel->deleteWrite($this->writeModel, $writeId);
        if($delWriteResult <= 0) {
            $message .= '정상적으로 게시글을 삭제하는데 실패하였습니다.(글 삭제)';
        }

        return $message;
    }

    /**
     * 리스트 - 선택 삭제
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function selectedDelete(Request $request, $boardId, $writeId)
    {
        $ids = explode(',', $writeId);
        foreach($ids as $id) {
            $message = $this->deleteWriteCascade($boardId, $id);
            if($message != '') {
                return view('message', [
                    'message' => '('. $id. '번 글)'. $message,
                    'redirect' => route('board.index', $boardId),
                ]);
            }
        }

        $returnUrl = route('board.index', $boardId). ($request->page == 1 ? '' : '?page='. $request->page);
        return redirect($returnUrl);
    }

}