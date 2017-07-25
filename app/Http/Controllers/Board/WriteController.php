<?php

namespace App\Http\Controllers\Board;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Write;
use App\Point;
use App\Board;
use App\BoardFile;
use App\BoardGood;
use App\Comment;
use App\Notification;
use App\ReCaptcha;
use Auth;
use Cache;
use App\Services\RssFeed;
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

        $skin = $this->writeModel->board->skin ? : 'default';

        return viewDefault("board.$skin.index", $params);
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
        $params = $this->writeModel->getViewParams($this->writeModel, $boardId, $writeId, $request);

        // 댓글 데이터
        $comment = new Comment();
        $params = array_collapse([$params, $comment->getCommentsParams($this->writeModel, $boardId, $writeId, $request)]);

        // 전체 목록 보기 선택시 목록 데이터
        if($this->writeModel->board->use_list_view) {
            $params = array_collapse([$params, $this->writeModel->getIndexParams($this->writeModel, $request)]);
        }
        // 이전글, 다음글 데이터 추가
        $params = array_collapse([$params, $this->writeModel->getPrevNextView($this->writeModel, $boardId, $writeId, $request)]);

        // 요청 URI 추가
        $params = array_add($params, 'requestUri', $request->getRequestUri());

        $skin = $this->writeModel->board->skin ? : 'default';

        return viewDefault("board.$skin.view", $params);
    }

    // 글 보기 중 링크 연결
    public function link($boardId, $writeId, $linkNo)
    {
        $linkUrl = $this->writeModel->beforeLink($this->writeModel, $writeId, $linkNo);

        return view('board.link', [ 'linkUrl' => $linkUrl ]);
    }

    // 추천/비추천 ajax 메서드
    public function good($boardId, $writeId, $good)
    {
        $count = $this->boardGoodModel->good($this->writeModel, $writeId, $good);

        return [ 'count' => $count ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $boardId)
    {
        $params = $this->writeModel->getCreateParams($this->writeModel, $request);
        $skin = $this->writeModel->board->skin ? : 'default';

        return viewDefault("board.$skin.form", $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $boardId)
    {
        if(auth()->guest() || (!auth()->user()->isSuperAdmin() && $this->writeModel->board->use_recaptcha)) {
			ReCaptcha::reCaptcha($request);
		}
        $writeId = $this->writeModel->storeWrite($this->writeModel, $request);

        if(count($request->attach_file) > 0) {
            $this->boardFileModel->createBoardFiles($request, $boardId, $writeId);
        }

        if(cache('config.email.default')->emailUse && $this->writeModel->board->use_email) {
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
    public function edit($boardId, $writeId, Request $request)
    {
        $params = $this->writeModel->getEditParams($boardId, $writeId, $this->writeModel, $request);
        $skin = $this->writeModel->board->skin ? : 'default';

        return viewDefault("board.$skin.form", $params);
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
        $fileCount = 0;
        if(count($request->file_del) > 0 || count($request->attach_file) > 0) {
            // 첨부 파일 변경
            $fileCount = $this->boardFileModel->updateBoardFiles($request, $boardId, $writeId);
        }
        // 게시 글 수정
        $this->writeModel->updateWrite($this->writeModel, $request, $writeId, $fileCount);

        return redirect(route('board.view', ['boardId' => $boardId, 'writeId' => $writeId] ));
    }

    /**
     * Show the form for editing the specified resource.
     * 글 답변 폼 연결
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createReply($boardId, $writeId, Request $request)
    {
        $params = $this->writeModel->getReplyParams($boardId, $writeId, $this->writeModel, $request);
        $skin = $this->writeModel->board->skin ? : 'default';

        return viewDefault("board.$skin.form", $params);
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

		$this->writeModel->checkReply($this->writeModel, $writeId);
		$this->writeModel->checkComment($this->writeModel, $writeId);

		try {
			$this->deleteWriteCascade($boardId, $writeId);
		} catch (Exception $e) {
			$redirect = route('board.index', $boardId);
			alertRedirect($e->getMessage(), $redirect);
		}

        $returnUrl = route('board.index', $boardId). ($request->page == 1 ? '' : '?page='. $request->page);
        return redirect($returnUrl);
    }

    // 게시글 삭제하면서 게시글에 종속된 것들도 함께 삭제
    private function deleteWriteCascade($boardId, $writeId)
    {
        $message = '';
        // 부여되었던 포인트 삭제 및 조정 반영
        $write = $this->writeModel->find($writeId);
        if($write->user_id)  {
            $point = new Point();
            $point->deleteWritePoint($this->writeModel, $boardId, $writeId);
        }
        // 서버에서 파일 삭제, 썸네일 삭제, 에디터 첨부 이미지 파일, 썸네일 삭제, 파일 테이블 삭제
        $delFileResult = $this->boardFileModel->deleteWriteAndAttachFile($boardId, $writeId);
        if( array_search(false, $delFileResult) === false ) {
            abort(500, '정상적으로 게시글을 삭제하는데 실패하였습니다.(첨부 파일 삭제)');
        }
        // 게시글 삭제
        $delWriteResult = $this->writeModel->deleteWrite($this->writeModel, $writeId);
        if($delWriteResult <= 0) {
            abort(500, '정상적으로 게시글을 삭제하는데 실패하였습니다.(게시글 삭제)');
        }

        return $message;
    }

    /**
     * 게시판 글 목록 - 선택 삭제
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function selectedDelete(Request $request, $boardId, $writeId)
    {
        $ids = explode(',', $writeId);
        foreach($ids as $id) {
			try {
				$this->deleteWriteCascade($boardId, $writeId);
			} catch (Exception $e) {
				$redirect = route('board.index', $boardId);
				alertRedirect("($id번 글) ". $e->getMessage(), $redirect);
			}
        }

        $returnUrl = route('board.index', $boardId). ($request->page == 1 ? '' : '?page='. $request->page);
        return redirect($returnUrl);
    }

	// 제목과 내용에 금지단어가 있는지 검사
	public function filter(Request $request)
    {
        return $this->writeModel->banWordFilter($request);
    }

    // RSS 보기
    public function rss(Request $request, $boardId, RssFeed $feed)
    {
        $rss = $feed->getRSS($boardId);

        return response($rss)
            ->header('Content-type', 'text/xml')
            ->header('Cache-Control', 'no-cache, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('charset', 'utf-8');
    }

}
