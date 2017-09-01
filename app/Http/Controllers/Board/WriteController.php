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

    public function __construct(Request $request, Write $write, BoardFile $boardFile, BoardGood $boardGood)
    {
        $this->writeModel = $write;
        $this->writeModel->board = Board::getBoard($request->boardName, 'table_name');
        $this->writeModel->setTableName($request->boardName);
        $this->boardFileModel = $boardFile;
        $this->boardGoodModel = $boardGood;
    }
    /**
     * Display a listing of the resource.
     *
     * @param string $boardName
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $boardName)
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
    public function view(Request $request, $boardName, $writeId)
    {
        $board = Board::getBoard($boardName, 'table_name');
        // 글 보기 데이터
        $params = $this->writeModel->getViewParams($this->writeModel, $writeId, $request);

        // 댓글 데이터
        $comment = new Comment();
        $params = array_collapse([$params, $comment->getCommentsParams($this->writeModel, $writeId, $request)]);

        // 전체 목록 보기 선택시 목록 데이터
        if($board->use_list_view) {
            $params = array_collapse([$params, $this->writeModel->getIndexParams($this->writeModel, $request)]);
        } else {
            $params = array_add($params, 'currenctCategory', '');
        }
        // 이전글, 다음글 데이터 추가
        $params = array_collapse([$params, $this->writeModel->getPrevNextView($this->writeModel, $writeId, $request)]);

        // 요청 URI 추가
        $params = array_add($params, 'requestUri', $request->getRequestUri());

        // 현재 사용자 추가
        $params = array_add($params, 'user', auth()->user());

        $skin = $board->skin ? : 'default';

        return viewDefault("board.$skin.view", $params);
    }

    // 글 보기 중 링크 연결
    public function link($boardName, $writeId, $linkNo)
    {
        $linkUrl = $this->writeModel->beforeLink($this->writeModel, $writeId, $linkNo);

        return view('board.link', [ 'linkUrl' => $linkUrl ]);
    }

    // 추천/비추천 ajax 메서드
    public function good($boardName, $writeId, $good)
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
    public function create(Request $request, $boardName)
    {
        $params = $this->writeModel->getCreateParams($request);
        $skin = $this->writeModel->board->skin ? : 'default';

        return viewDefault("board.$skin.form", $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $boardName)
    {
        $rules = $this->rules();
        $messages = $this->messages();

        if(auth()->guest()) {
            $rules = array_add($rules, 'name', 'required|alpha_dash|max:20');
            $rules = array_add($rules, 'password', 'required|max:20');
        }

        if(!$this->writeModel->board->use_dhtml_editor
            || auth()->guest()
            || !auth()->user()->isSuperAdmin()
            || auth()->user()->level < $this->writeModel->board->html_level) {
            if($this->writeModel->board->write_min) {
                $rules['content'] .= '|min:'.$this->writeModel->board->write_min;
            }
            if($this->writeModel->board->write_max) {
                $rules['content'] .= '|max:'.$this->writeModel->board->write_max;
            }
        }

        if($this->writeModel->board->use_category) {
            $rules = array_add($rules, 'ca_name', 'required');
        }

        // 공백 제거
        $request->merge([
            'subject' => trim($request->subject),
            'content' => trim($request->content)
        ]);

        $this->validate($request, $rules, $messages);

        if(auth()->guest() || (!auth()->user()->isBoardAdmin($this->writeModel->board) && $this->writeModel->board->use_recaptcha)) {
            ReCaptcha::reCaptcha($request);
        }

        $writeId = $this->writeModel->storeWrite($this->writeModel, $request);

        if(count($request->attach_file) > 0) {
            try {
                $this->boardFileModel->createBoardFiles($request, $this->writeModel->board->id, $writeId);
            } catch(Exception $e) {
            }
        }

        if(cache('config.email.default')->emailUse && $this->writeModel->board->use_email && $request->mail == 'mail') {
            $notification = new Notification();
            $notification->sendWriteNotification($this->writeModel, $writeId);
        }

        return redirect(route('board.view', ['boardId' => $boardName, 'writeId' => $writeId] ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $boardName, $writeId)
    {
        $params = $this->writeModel->getEditParams($writeId, $this->writeModel, $request);
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
    public function update(Request $request, $boardName, $writeId)
    {
        $rules = $this->rules();
        $messages = $this->messages();

        if(auth()->guest()) {
            $rules = array_add($rules, 'name', 'required|alpha_dash|max:20');
        }

        if(!$this->writeModel->board->use_dhtml_editor
            || auth()->guest()
            || !auth()->user()->isSuperAdmin()
            || auth()->user()->level < $this->writeModel->board->html_level) {
            if($this->writeModel->board->write_min) {
                $rules['content'] .= '|min:'.$this->writeModel->board->write_min;
            }
            if($this->writeModel->board->write_max) {
                $rules['content'] .= '|max:'.$this->writeModel->board->write_max;
            }
        }

        // 공백 제거
        $request->merge([
            'subject' => trim($request->subject),
            'content' => trim($request->content)
        ]);

        $this->validate($request, $rules, $messages);

        $fileCount = 0;
        if(count($request->file_del) > 0 || count($request->attach_file) > 0) {
            // 첨부 파일 변경
            $fileCount = $this->boardFileModel->updateBoardFiles($request, $this->writeModel->board->id, $writeId);
        }
        // 게시 글 수정
        $this->writeModel->updateWrite($this->writeModel, $request, $writeId, $fileCount);

        return redirect(route('board.view', ['boardId' => $boardName, 'writeId' => $writeId] ));
    }

    /**
     * Show the form for editing the specified resource.
     * 글 답변 폼 연결
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createReply(Request $request, $boardName, $writeId)
    {
        $params = $this->writeModel->getReplyParams($writeId, $this->writeModel, $request);
        $skin = $this->writeModel->board->skin ? : 'default';

        return viewDefault("board.$skin.form", $params);
    }

    /**
     * 글보기 - 삭제
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $boardName, $writeId)
    {
        $message = $redirect = '';

        try {
            $this->writeModel->deleteWriteCascade($this->writeModel, $writeId);
        } catch (Exception $e) {
            $redirect = route('board.index', $boardName);
            return alertRedirect($e->getMessage(), $redirect);
        }

        $returnUrl = route('board.index', $boardName). ($request->page == 1 ? '' : '?page='. $request->page);
        return redirect($returnUrl);
    }

    /**
     * 게시판 글 목록 - 선택 삭제
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function selectedDelete(Request $request, $boardName, $writeId)
    {
        $ids = explode(',', $writeId);
        foreach($ids as $id) {
            try {
                $this->writeModel->deleteWriteCascade($this->writeModel, $id);
            } catch (Exception $e) {
                $redirect = route('board.index', $boardName);
                return alertRedirect("($id번 글) ". $e->getMessage(), $redirect);
            }
        }

        $returnUrl = route('board.index', $boardName). ($request->page == 1 ? '' : '?page='. $request->page);
        return redirect($returnUrl);
    }

    // RSS 보기
    public function rss(Request $request, $boardName, RssFeed $feed)
    {
        $rss = $feed->getRSS($boardName);

        return response($rss)
            ->header('Content-type', 'text/xml')
            ->header('Cache-Control', 'no-cache, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('charset', 'utf-8');
    }

    // 유효성 검사 규칙
    public function rules()
    {
        return [
            'email' => 'email|max:255|nullable',
            'homepage' => 'regex:/^(((http(s?))\:\/\/)?)([0-9a-zA-Z\-]+\.)+[a-zA-Z]{2,6}(\:[0-9]+)?(\/\S*)?$/|nullable',
            'subject' => 'required|max:255',
            'content' => 'required',
            'link1' => 'regex:/^(((http(s?))\:\/\/)?)([0-9a-zA-Z\-]+\.)+[a-zA-Z]{2,6}(\:[0-9]+)?(\/\S*)?$/|nullable',
            'link2' => 'regex:/^(((http(s?))\:\/\/)?)([0-9a-zA-Z\-]+\.)+[a-zA-Z]{2,6}(\:[0-9]+)?(\/\S*)?$/|nullable'
        ];
    }

    // 에러 메세지
    public function messages()
    {
        return [
            'name.required' => '이름을 입력해 주세요.',
            'name.alpha_dash' => '이름에 영문자, 한글, 숫자, 대쉬(-), 언더스코어(_)만 입력해 주세요.',
            'name.max' => '이름은 :max자리를 넘길 수 없습니다.',
            'password.required' => '비밀번호를 입력해 주세요.',
            'password.max' => '비밀번호는 :max자리를 넘길 수 없습니다.',
            'email.email' => '이메일에 올바른 Email양식으로 입력해 주세요.',
            'email.max' => '이메일은 :max자리를 넘길 수 없습니다.',
            'homepage.regex' => '홈페이지에 올바른 url 형식으로 입력해 주세요.',
            'ca_name.required' => '카테고리를 선택해 주세요.',
            'subject.required' => '제목을 입력해 주세요.',
            'subject.max' => '제목은 :max자리를 넘길 수 없습니다.',
            'content.required' => '내용을 입력해 주세요.',
            'content.min' => '내용은 :min글자 이상 쓰셔야 합니다.',
            'content.max' => '내용은 :max글자 이하로 쓰셔야 합니다.',
            'link1.regex' => '첫번째 링크에 올바른 url 형식으로 입력해 주세요.',
            'link2.regex' => '두번째 링크에 올바른 url 형식으로 입력해 주세요.',
        ];
    }

}
