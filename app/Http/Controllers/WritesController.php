<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
use App\Contracts\BoardInterface;
use App\Contracts\WriteInterface;
use App\Models\BoardFile;
use App\Models\BoardGood;
use App\Models\Comment;
use App\Models\Notice;
use App\Models\RssFeed;

class WritesController extends Controller
{
    public $writeModel;
    public $boardFileModel;
    public $boardGoodModel;

    public function __construct(Request $request, WriteInterface $write, BoardInterface $board, BoardFile $boardFile, BoardGood $boardGood)
    {
        $this->writeModel = $write;
        $this->writeModel->board = $board->getBoard($request->boardName, 'table_name');
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
        $theme = cache('config.theme')->name ? : 'default';
        $params['skin'] = $skin;
        $params['theme'] = $theme;

        return viewDefault("$theme.boards.$skin.index", $params);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view(Request $request, $boardName, $writeId)
    {
        $board = $this->writeModel->board;
        $write = $this->writeModel::getWrite($board->id, $writeId);
        // 글 읽기전 조회수 증가, 포인트 계산 이벤트 Fire
        event(new \App\Events\BeforeRead($request, $board, $write));

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

        // Open Graph image 추출
        $params = array_add($params, 'ogImage', pullOutImage($write->content, $board->id, $write->id, $write->file));

        $skin = $this->writeModel->board->skin ? : 'default';
        $theme = cache('config.theme')->name ? : 'default';
        $params['skin'] = $skin;
        $params['theme'] = $theme;

        return viewDefault("$theme.boards.$skin.view", $params);
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
        $theme = cache('config.theme')->name ? : 'default';
        $params['skin'] = $skin;
        $params['theme'] = $theme;

        return viewDefault("$theme.boards.$skin.form", $params);
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
            'content' => $this->writeModel->board->use_dhtml_editor ? trim(convertContent($request->content, 1)) : trim($request->content),
        ]);

        $this->validate($request, $rules, $messages);

        $write = $this->writeModel->storeWrite($this->writeModel, $request);

        if(notNullCount($request->attach_file) > 0) {
            try {
                $this->boardFileModel->createBoardFiles($request, $this->writeModel->board->id, $write->id);
            } catch(Exception $e) {
            }
        }

        $notice = new Notice();
        // 기본환경설정에서 이메일 사용을 하고, 해당 게시판에서 메일발송을 사용하고, 글쓴이가 답변메일을 받겠다고 하면
        if(cache('config.email.default')->emailUse && $this->writeModel->board->use_email && $request->mail == 'mail') {
            $notice->sendWriteNotice($this->writeModel, $write->id);
        }

        // 글쓰기 후 이벤트 처리
        fireEvent('afterStoreWrite', $this->writeModel, $write->id);

        return redirect(route('board.view', ['boardId' => $boardName, 'writeId' => $write->id] ));
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
        $theme = cache('config.theme')->name ? : 'default';
        $params['skin'] = $skin;
        $params['theme'] = $theme;

        return viewDefault("$theme.boards.$skin.form", $params);
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
            'content' => $this->writeModel->board->use_dhtml_editor ? trim(convertContent($request->content, 1)) : trim($request->content),
        ]);

        $this->validate($request, $rules, $messages);

        $fileCount = $this->writeModel::getWrite($this->writeModel->board->id, $writeId)->file;
        if(notNullCount($request->file_del) > 0 || notNullCount($request->attach_file) > 0) {
            // 첨부 파일 변경
            $fileCount = $this->boardFileModel->updateBoardFiles($request, $this->writeModel->board->id, $writeId);
        }
        // 게시 글 수정
        $this->writeModel->updateWrite($this->writeModel, $request, $writeId, $fileCount);

        $queryString = $request->filled('queryString') ? '?'. $request->queryString : '';

        $returnUrl = route('board.view', ['boardId' => $boardName, 'writeId' => $writeId] ). $queryString;

        return redirect($returnUrl);
    }

    /**
     * 글보기 - 삭제
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $boardName, $writeId)
    {
        $redirect = '';

        try {
            $this->writeModel->deleteWriteCascade($this->writeModel, $writeId);
        } catch (Exception $e) {
            $redirect = route('board.index', $boardName);
            return alertRedirect($e->getMessage(), $redirect);
        }

        $returnUrl = route('board.index', $boardName). ($request->page == 1 ? '' : '?page='. $request->page);

        return redirect($returnUrl);
    }

    // 글 보기 중 링크 연결
    public function link($boardName, $writeId, $linkNo)
    {
        $linkUrl = $this->writeModel->beforeLink($this->writeModel, $writeId, $linkNo);

        $theme = cache('config.theme')->name ? : 'default';

        return viewDefault("$theme.boards.link", compact('linkUrl'));
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
        event(new \App\Events\WriteReply($request, $boardName, $writeId));

        $params = $this->writeModel->getReplyParams($writeId, $this->writeModel, $request);
        $skin = $this->writeModel->board->skin ? : 'default';
        $theme = cache('config.theme')->name ? : 'default';
        $params['skin'] = $skin;
        $params['theme'] = $theme;

        return viewDefault("$theme.boards.$skin.form", $params);
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
        event(new \App\Events\GetRssView($request, $this->writeModel->board));

        $rss = $feed->getRSS($boardName);

        return response($rss)
            ->header('Content-type', 'text/xml')
            ->header('Cache-Control', 'no-cache, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('charset', 'utf-8');
    }

    // ajax - 게시글에 제목과 내용에 금지단어가 포함되어있는지 검사
    public function boardFilter(Request $request)
    {
        $subject = $request->subject;
        $content = $request->content;

        $filterStrs = explode(',', trim(implode(',', cache("config.board")->filter)));
        $returnArr['subject'] = '';
        $returnArr['content'] = '';
        foreach($filterStrs as $str) {
            // 제목 필터링 (찾으면 중지)
            $pos = stripos($subject, $str);
            if ($pos !== false) {
                $returnArr['subject'] = $str;
                break;
            }

            // 내용 필터링 (찾으면 중지)
            $pos = stripos($content, $str);
            if ($pos !== false) {
                $returnArr['content'] = $str;
                break;
            }
        }

        return $returnArr;
    }

    // 유효성 검사 규칙
    public function rules()
    {
        return [
            'email' => 'email|max:255|nullable',
            'homepage' => 'regex:'. config('laon.URL_REGEX'). '|nullable',
            'subject' => 'required|max:255',
            'content' => 'required',
            'link1' => 'regex:'. config('laon.URL_REGEX'). '|nullable',
            'link2' => 'regex:'. config('laon.URL_REGEX'). '|nullable',
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
