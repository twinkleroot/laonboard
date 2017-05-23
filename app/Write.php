<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;
use DB;
use File;
use Carbon\Carbon;
use Exception;
use App\User;
use App\Board;
use App\Point;
use App\Common\Util;
use App\Common\StrEncrypt;
use App\Common\CustomPaginator;
use App\Autosave;
use App\BoardFile;


class Write extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $table;
    public $board;
    public $point;

    public function __construct($boardId, $attributes = [])
    {
        $this->board = Board::find($boardId);
        $this->point = new Point();

        parent::__construct($attributes);
    }

    // write 모델의 테이블 이름을 지정
    public function setTableName($tableName)
    {
        $this->table = 'write_' . $tableName;
    }

    public function getTableName()
    {
        return $this->table;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // (게시판) index 페이지에서 필요한 파라미터 가져오기
    public function getBbsIndexParams($writeModel, $request)
    {
        // 글 목록에서 글 보기로 넘어갈 때 가지고 있어야 할 파라미터를 가지는 배열
        $viewParams = [];

        // 전체 카테고리 리스트
        $categories = [];
        if($this->board->use_category == 1 && !is_null($this->board->category_list) ) {
            $categories = explode('|', $this->board->category_list);
        }

        // 현재 선택한 카테고리 구하기
        $queryStr = explode('category=', urldecode($request->fullUrl()));
        $currenctCategory = '';
        if(count($queryStr) > 1) {
            $currenctCategory = explode('&', $queryStr[1])[0];
            $viewParams['category'] = 'category='. $currenctCategory;
        }

        // 검색 기준
        $kind = '';
        if($request->has('kind')) {
            $kind = $request->kind;
            $viewParams['kind'] = 'kind='. $kind;
        }

        // 검색어
        $keyword = '';
        if($request->has('keyword')) {
            $keyword = $request->keyword;
            $viewParams['keyword'] = 'keyword='. $keyword;
        }

        $userLevel = is_null(Auth::user()) ? 1 : Auth::user()->level;
        $notices = explode(',', $this->board->notice);

        $result = [];
        try {
            $result = $this->getWrites($writeModel, $request, $kind, $keyword, $currenctCategory);
            if($result['message'] != '') {
                return [
                    'message' => $result['message'],
                ];
            } else {
                if($result['writes']->currentPage() > 1) {
                    $viewParams['page'] = 'page='. $result['writes']->currentPage();
                }
            }
        } catch (Exception $e) {
            return [
                'message' => '글이 존재하지 않습니다.\\n글이 삭제되었거나 이동하였을 수 있습니다.',
                'redirect' => '/'
            ];
        }

        return [
            'board' => $this->board,
            'writes' => $result['writes'],
            'userLevel' => $userLevel,
            'kind' => $kind,
            'keyword' => $keyword,
            'notices' => $notices,
            'categories' => $categories,
            'currenctCategory' => $currenctCategory,
            'request' => $request,
            'search' => $request->has('keyword') ? 1 : 0,
            'viewParams' => implode('&', $viewParams),
        ];
    }

    // (게시판 리스트) 해당 커뮤니티 게시판 모델을 가져온다. (검색 포함)
    public function getWrites($writeModel, $request, $kind, $keyword, $currenctCategory)
    {
        // select ~ from ~ where까지 얻어온다.
        $query = $this->getWritesWhere($writeModel, $kind, $keyword, $currenctCategory);

        // 어떤 필드를 기준으로 정렬할 것인지
        $sortField = $this->getSortField();

        // 결과물에 공지사항이 있는지 검사한다.
        $hasNotice = $this->hasNotice($writeModel, $kind, $keyword, $currenctCategory);

        // 최종 리스트 컬렉션을 가져온다.
        $writes;
        if($hasNotice) {
            $writes = $this->customPaging($request, $query, $sortField);
        } else {
            $writes = $query->orderByRaw($sortField)->paginate($this->board->page_rows);
        }

        // 가져온게시글 가공
        // 1. 뷰에 내보내는 아이디 검색의 링크url에는 암호화된 id를 링크로 건다.
        // 2. 검색일 경우 검색 키워드 색깔 표시를 다르게 한다.
        foreach($writes as $write) {
            $write->user_id = encrypt($write->user_id);     // 라라벨 기본 지원 encrypt
            $write->subject = Util::searchKeyword($keyword, $write->subject);
        }

        // 페이징 버튼의 경로 지정 (항상 목록으로 이동하도록 하기)
        $writes->withPath('/board/'.$this->board->id);

        return [
            'writes' => $writes,
            'message' => '',
        ];
    }

    // (게시판 리스트) select ~ from ~ where까지 얻어온다.
    public function getWritesWhere($writeModel, $kind, $keyword, $currenctCategory)
    {
        // 기본 ( 공지는 기본만 가져간다. )
        $query = $writeModel
                ->selectRaw($writeModel->table.'.*, users.level as user_level')
                ->leftJoin('users', 'users.id', '=', $writeModel->table.'.user_id');

        // + 카테고리
        if($currenctCategory != '') {
            $query = $query->where('ca_name', $currenctCategory);
        }

        // + [카테고리] + 검색
        if($kind != '' && $keyword != '') {
            if($kind == 'user_id') {
                // 암호화된 user_id를 복호화해서 검색한다.
                $userId = decrypt($keyword);    // 라라벨 기본 지원 decrypt

                $query = $query->where('user_id', $userId);
            } else if(str_contains($kind, '||')) { // 제목 + 내용으로 검색
                $kinds = explode('||', preg_replace("/\s+/", "", $kind));
                // 검색 쿼리 붙이기
                foreach($kinds as $kind) {
                    $query = $query->where($kind, 'like', '%'.$keyword.'%', 'or');
                }
            // 코멘트 검색이 select box에 있는 경우
            } else if(str_contains($kind, ',')) {
                $kinds = explode(',', preg_replace("/\s+/", "", $kind));
                $user = User::where($kinds[0], $keyword)->first();
                // 검색 쿼리 붙이기
                if(!is_null($user)) {
                    $query = $query->where('user_id', $user->id)
                                   ->where('is_comment', $kinds[1]);
                } else {
                    return [
                        'writes' => null,
                        'message' => $keyword. ' 사용자가 존재하지 않습니다.'
                    ];
                }
            // 단독 키워드 검색(제목, 내용)
            } else {
                $query = $query->where($kind, 'like', '%'.$keyword.'%');
            }
        }

        return $query;
    }

    // order by 절에 들어갈 내용 가져오기
    private function getSortField()
    {
        return is_null($this->board->sort_field) ? 'num, reply' : $this->board->sort_field;
    }

    // 수동 페이징
    public function customPaging($request, $query, $sortField)
    {
        $currentPage = $request->has('page') ? $request->page : 1 ;
        // 공지 글은 가장 앞에 나와야 하므로 컬렉션의 위치를 조절해서 수동으로 페이징 한다.
        $totalWrites = $query->orderByRaw($sortField)->get();

        // 컬렉션 분할 (공지 + 그 외)
        $notices = explode(',', $this->board->notice);
        // 공지 게시물들
        $noticeWrites = collect();
        $noticeWrites = $totalWrites->filter(function ($value, $key) {
            $notices = explode(',', $this->board->notice);
            return in_array($value->id, $notices);
        });
        // 그 외 게시물들
        $filteredWrites = collect();
        $filteredWrites = $totalWrites->reject(function ($value, $key) {
            $notices = explode(',', $this->board->notice);
            return in_array($value->id, $notices);
        });

        // 컬렉션 합치기
        $mergeWrites = $noticeWrites->merge($filteredWrites);

        // 수동으로 페이징할 땐 컬렉션을 잘라주어야 한다.
        $sliceWrites = $mergeWrites->slice($this->board->page_rows * ($currentPage - 1), $this->board->page_rows);

        $writes = new CustomPaginator($sliceWrites, count($mergeWrites), $this->board->page_rows, $currentPage);

        return $writes;
    }

    // 글 목록 결과물에 공지사항이 있는지 검사한다.
    private function hasNotice($writeModel, $kind, $keyword, $currenctCategory)
    {
        // select ~ from ~ where까지 얻어온다.
        $query = $this->getWritesWhere($writeModel, $kind, $keyword, $currenctCategory);

        $notices = explode(',', $this->board->notice);
        for($i=0; $i<count($notices); $i++) {
            $notices[$i] = (integer)$notices[$i];
        }
        $result = $query->whereIn($writeModel->table. '.id', $notices)->get();

        return count($result) > 0 ? true : false;
    }

    public function getViewParams($request, $boardId, $writeId, $writeModel)
    {
        $write = $writeModel->find($writeId);

        // 조회수 증가, 포인트 부여
        $result = $this->beforeRead($write, $request);

        if(is_string($result)) {
            return [ 'message' => $result ];
        } else {
            $write->hit = $result;
        }

        // 글쓰기 할때 html 체크에 따라 글 내용 보여주는 방식을 다르게 한다.
        // html = 0 - 체크안함, 1 - 체크 후 취소, 2 - 체크 후 확인
        $html = 0;
        if (strpos($write->option, 'html1') !== false) {
            $html = 1;
        } else if (strpos($write->option, 'html2') !== false) {
            $html = 2;
        }

         // 에디터를 사용하면서 html에 체크하지 않았을 때
        if($this->board->use_dhtml_editor && $html == 0) {
            $write->content = Util::convertContent($write->content, 2);
        } else {
            $write->content = Util::convertContent($write->content, $html);
        }

        // 관리자 여부에 따라 ip 다르게 보여주기
        if( !session()->get('admin')) {
            if ( !is_null($write->ip)) {
                $write->ip = preg_replace("/([0-9]+).([0-9]+).([0-9]+).([0-9]+)/", config('gnu.IP_DISPLAY'), $write->ip);
            }
        }

        // 서명 사용하면 글쓴이의 서명을 담는다.
        $signature = '';
        if($this->board->use_signature && $write->user_id > 0) {
            $user = User::find($write->user_id);
            if(!is_null($user)) {
                $signature = $user->signature;
            }
        }

        // 첨부 파일과 이미지 파일 분류
        $imgExtension = Config::getConfig('config.board')->imageExtension;
        $boardFiles = [];
        $imgFiles = [];
        if($write->file > 0) {
            $boardFiles = BoardFile::where([
                'board_id' => $boardId,
                'write_id' => $writeId,
            ])->get();

            foreach($boardFiles as $boardFile) {
                // 첨부파일이 이미지라면 업로드된 파일의 확장자를 가져와서
                // 게시판 기본설정에 설정한 업로드 가능한 이미지 확장자인지 검사하고
                // 이미지가 아니라면 통과시킨다.
                $filePiece = explode('.', $boardFile->file);
                if( !str_contains($imgExtension, last($filePiece))) {
                    continue;
                }
                // 이미지 경로를 가져와서 썸네일만든 후 서버에 저장
                $imageFileInfo = Util::getViewThumbnail($this->board, $boardFile->file, $this->board->table_name);

                array_push($imgFiles, $imageFileInfo);
                // 이미지 파일은 파일 첨부 컬렉션에서는 제외
                $boardFiles = $boardFiles->reject(function ($value, $key) use ($boardFile) {
                    return $value->file == $boardFile->file;
                });
            }

        }

        // 에디터로 업로드한 이미지 경로를 추출해서 내용의 img 태그 부분을 교체한다.
        $write->content = $this->includeImagePathByEditor($write->content);

        return [
            'board' => $this->board,
            'view' => $write,
            'request' => $request,
            'signature' => $signature,
            'boardFiles' => $boardFiles,
            'imgFiles' => $imgFiles,
        ];
    }

    // 에디터로 업로드한 이미지 경로를 추출해서 내용의 img 태그 부분을 교체한다.
    public function includeImagePathByEditor($content)
    {
        // 에디터로 업로드한 이미지 경로를 추출한다.
        $pattern = "/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i";
        preg_match_all($pattern, $content, $matches);

        for($i=0; $i<count($matches[1]); $i++) {
            // 썸네일 만들기
            $imageFileInfo = Util::getViewThumbnail($this->board, basename($matches[1][$i]), 'editor');

            $html = "a href='". route('image.original'). "?type=editor&amp;imageName=". str_replace("thumb-", "", $imageFileInfo['name']). "'"
                    . " class='viewOriginalImage' width='". $imageFileInfo[0]. "' height='". $imageFileInfo[1]. "' target='viewImage'>"
                    . "<img src='/storage/editor/". $imageFileInfo['name']. "' /></a";
            // 글 내용에 이미지 원본보기 링크와 이미지경로를 넣어준다.
            $content = preg_replace("<img src=\"".$matches[1][$i]."\" />", $html, $content);
        }

        return $content;
    }

    // 이전 글, 다음 글 경로 가져오기
    public function getPrevNextView($writeModel, $boardId, $writeId, $request)
    {
        $write = $writeModel->find($writeId);
        // 파라미터 구하기
        $params = Util::getParamsFromQueryString($request->server('QUERY_STRING'));

        $kind = isset($params['kind']) ? $params['kind'] : '';
        $keyword = isset($params['keyword']) ? $params['keyword'] : '';
        $currenctCategory = isset($params['category']) ? $params['category'] : '';

        // 이전 글 얻음
        $sortField = 'num desc, reply desc';    // order by
        $query = $this->getWritesWhere($writeModel, $kind, $keyword, $currenctCategory)
            ->where('is_comment', 0);
        $prevWrite = $query->where('num', $write->num)
            ->where('reply', '<', is_null($write->reply) ? '' : $write->reply)
            ->orderByRaw($sortField)
            ->first();
        if(is_null($prevWrite)) {
            $query = $this->getWritesWhere($writeModel, $kind, $keyword, $currenctCategory)
                ->where('is_comment', 0);
            $prevWrite = $query->where('num', '<', $write->num)
                ->orderByRaw($sortField)->first();
        }

        // 다음 글 얻음
        $sortField = 'num, reply';              // order by
        $query = $this->getWritesWhere($writeModel, $kind, $keyword, $currenctCategory)
            ->where('is_comment', 0);
        $nextWrite = $query->where('num', $write->num)
            ->where('reply', '>', is_null($write->reply) ? '' : $write->reply)
            ->orderByRaw($sortField)
            ->first();
        if(is_null($nextWrite)) {
            $query = $this->getWritesWhere($writeModel, $kind, $keyword, $currenctCategory)
                ->where('is_comment', 0);
            $nextWrite = $query->where('num', '>', $write->num)
                ->orderByRaw($sortField)->first();
        }

        // 구한 이전 글 정보로 이전 글 url을 만든다.
        if(is_null($prevWrite)) {
            $prevUrl = '';
        } else {
            $prevUrl = $this->getPrevNextUrl($boardId, $request, $prevWrite);
        }

        // 구한 다음 글 정보로 다음 글 url을 만든다.
        if(is_null($nextWrite)) {
            $nextUrl = '';
        } else {
            $nextUrl = $this->getPrevNextUrl($boardId, $request, $nextWrite);
        }

        return [
            'prevUrl' => $prevUrl,
            'nextUrl' => $nextUrl,
        ];
    }

    // 이전 or 다음 글 url을 만든다.
    public function getPrevNextUrl($boardId, $request, $write)
    {
        $url = route('board.view', ['boardId' => $boardId, 'writeId' => $write->id ]);

        if($request->server('QUERY_STRING') != '') {
           $url .= '?'. $request->server('QUERY_STRING');
        }

        return $url;
    }

    // 글 읽기 전 프로세스
    public function beforeRead($write, $request)
    {
        $sessionName = 'session_view_'. $this->board->table_name. '_'. $write->id;
        $hit = $write->hit;
        $user = auth()->user();
        if(!session()->get($sessionName) && $user->id != $write->user_id) {
            // 조회수 증가
            $hit = $this->increaseHit($write);
            // 포인트 계산(차감)
            $message = $this->calculatePoint($write, $request, 'read');

            if($message != '') {
                return $message;
            }

            session()->put($sessionName, true);
        }

        return $hit;
    }

    // 조회수 증가
    public function increaseHit($write)
    {
        $hit = $write->hit + 1;
        DB::table('write_'. $this->board->table_name)
            ->where('id', $write->id)
            ->update(['hit' => $hit]);

        return $hit;
    }

    // 소비성 포인트 계산(글 읽기, 파일 다운로드)
    public function calculatePoint($write, $request, $type)
    {
        $user = auth()->user();
        $boardlevel = 0;
        $useBoardPoint = 0;
        $action = '';
        $contentPiece = '';
        switch ($type) {
            case 'read':
                $boardlevel = $this->board->read_level;
                $boardPoint = $this->board->read_point;
                $action = '읽기';
                $contentPiece = ' 글읽기';
                break;
            case 'download':
                $boardlevel = $this->board->download_level;
                $boardPoint = $this->board->download_point;
                $action = '다운로드';
                $contentPiece = ' 파일 다운로드';
                break;
            default:
                # code...
                break;
        }
        // 작성자가 본인이면 통과
        if($write->user_id > 0 && $write->user_id == $user->id) {
            ;
        } else if(is_null($user) && $boardlevel == 1 && $write->ip == $request->ip()) {
            ;
        } else {
            // 포인트 사용 && 소모되는 포인트가 있는지 && 현재 사용자가 갖고 있는 포인트로 사용 가능한지 검사
            if (Config::getConfig('config.homepage')->usePoint
                && $boardPoint
                && $user->point + $boardPoint < 0) {
                    return '보유하신 포인트('.number_format($user->point).')가 없거나 모자라서'. $contentPiece. '('.number_format($boardPoint).')가 불가합니다.\\n\\n포인트를 모으신 후 다시'.$contentPiece.' 해 주십시오.';
            }

            // 포인트 계산하기
            // 포인트 부여(글 읽기, 파일 다운로드)
            $this->point->insertPoint($user->id, $boardPoint,
                $this->board->subject . ' ' . $write->id . $contentPiece, $this->board->table_name, $write->id, $action);
        }

        return '';
    }

    // 글 읽기 중 링크 연결
    public function beforeLink($writeModel, $writeId, $linkNo)
    {
        $write = $writeModel->find($writeId);
        $linkUrl = '';
        if(!$write['link'.$linkNo]) {
            return [
                'message' => '링크가 없습니다.',
            ];
        }

        // 링크 연결수 증가
        $sessionName = 'session_link_'. $this->board->table_name. '_'. $write->id. '_'. $linkNo;
        $user = auth()->user();
        if(!session()->get($sessionName)) {
            $this->increaseLinkHit($write, $linkNo);
            session()->put($sessionName, true);
        }

        // 글에 있는 링크를 링크 페이지로 넘김
        $linkUrl = $write['link'.$linkNo];

        return [
            'linkUrl' => $linkUrl,
        ];
    }

    // 링크 연결수 증가
    public function increaseLinkHit($write, $linkNo)
    {
        $linkHit = $write['link'. $linkNo. '_hit'] + 1;
        DB::table('write_'. $this->board->table_name)
            ->where('id', $write->id)
            ->update(['link'. $linkNo. '_hit' => $linkHit]);

        return $linkHit;
    }

    // 다운로드시 처리할 내용
    public function beforeDownload($request, $writeModel, $boardId, $writeId, $fileNo)
    {
        $file = BoardFile::where([
            'board_id' => $boardId,
            'write_id' => $writeId,
            'board_file_no' => $fileNo,
            ])->first();

        $user = auth()->user();
        $write = $writeModel->find($writeId);
        $sessionName = 'session_download_'. $this->board->table_name. '_'. $write->id. '_'. $fileNo;
        if(session()->get('admin') || $user->id == $write->user_id) {   // 관리자나 작성자 본인이면 패스
            ;
        } else if(!session()->get($sessionName)) { // 사용자의 다운로드 세션이 존재하지 않는다면
            // 포인트 차감
            $message = $this->calculatePoint($write, $request, 'download');

            // 포인트 관련 에러 메세지가 있으면 출력함
            if($message != '') {
                return [ 'message' => $message ];
            }

            // 다운로드 횟수 증가
            $file->where([
                'board_id' => $boardId,
                'write_id' => $writeId,
                'board_file_no' => $fileNo,
            ])->update(['download' => $file->download + 1]);


            session()->put($sessionName, true);
        }

        return $file;
    }

    // (게시판) 글 쓰기 페이지에서 필요한 파라미터 가져오기
    public function getBbsCreateParams($writeModel)
    {
        $board = $this->board;
        $categories = [];
        if( !is_null($board->category_list) ) {
            $categories = explode('|', $board->category_list);
        }

        $autosaveCount = 0;
        if(auth()->user()) {
            $autosaveCount = Autosave::getAutosaveCount();
        }

        return [
            'type' => 'create',
            'board' => $board,
            'categories' => $categories,
            'autosaveCount' => $autosaveCount,
        ];
    }

    // 글 수정할 때 필요한 파라미터 가져오기
    public function getEditParams($boardId, $writeId, $writeModel)
    {
        $write = $writeModel->find($writeId);

        $boardFiles = [];
        if($write->file > 0) {
            $boardFiles = BoardFile::where([
                'board_id' => $boardId,
                'write_id' => $writeId,
            ])->get();
        }
        foreach($boardFiles as $file) {
            $file->filesize = Util::getFileSize($file->filesize);
        }

        // 에디터로 업로드한 이미지 경로를 추출해서 내용의 img 태그 부분을 교체한다.
        $write->content = $this->includeImagePathByEditor($write->content);

        // 파일첨부 칸이 최소한 환경설정에서 설정한 대로 나올 수 있도록 file 값을 조정한다.
        $uploadedFileCount = $write->file;
        $configUploadFileCount = $this->board->upload_count;
        $write->file = $uploadedFileCount < $configUploadFileCount ? $configUploadFileCount : $uploadedFileCount;

        $createParams = $this->getBbsCreateParams($writeModel);
        $createParams['type'] = 'update';

        $params = [
            'write' => $write,
            'boardFiles' => $boardFiles,
        ];

        $params = array_collapse([$params, $createParams]);

        return $params;
    }

    // (게시판) 글 쓰기 -> 저장
    public function storeWrite($writeModel, $request)
    {
        $inputData = $request->all();
        $inputData = array_except($inputData, ['_token', 'file_content', 'attach_file', 'html', 'secret', 'mail', 'notice', 'uid']);
        $inputData = $this->convertSomeField($inputData);

        $options = [];
        $options['html'] = $request->has('html') ? $request->html : '';
        $options['secret'] = $request->has('secret') ? $request->secret : '';
        $options['mail'] = $request->has('mail') ? $request->mail : '';

        foreach($options as $key => $value) {
            if($value == '') {
                $options = array_except($options, [$key]);
            }
        }

        $user = Auth::user();
        $userId = 1;    // $userId가 1이면 비회원
        $name = '';
        $password = '';
        $minNum = $writeModel->min('num');
        $email = '';
        $homepage = '';

        // 회원 글쓰기 일 때
        if( !is_null($user) ) {
            // 실명을 사용할 때
            if($this->board->use_name && !is_null($user->name)) {
                $name = $user->name;
            } else {
                $name = $user->nick;
            }

            $userId = $user->id;
            $password = $user->password;
            $email = $user->email;
            $homepage = $user->homepage;
        } else {
            $email = $inputData['email'];
            $homepage = $inputData['homepage'];
        }

        $insertData = array_collapse([
            $inputData,
            [
                'user_id' => $userId,
                'name' => is_null($user) ? $inputData['name'] : $name,
                'email' => $email,
                'homepage' => $homepage,
                'password' => is_null($user) ? bcrypt($inputData['password']) : $password,
                'ip' => $request->ip(),
                'option' => count($options) > 0 ? implode(',', $options) : null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'file' => count($request->attach_file),
                'num' => $minNum - 1,
                'hit' => 1,
            ]
        ]);

        $writeModel->insert($insertData);
        $lastInsertId = DB::getPdo()->lastInsertId();   // 마지막에 삽입한 행의 id 값 가져오기
        $newWrite = $writeModel->where('id', $lastInsertId)->first();
        $toUpdateColumn = [
            'parent' => $newWrite->id,
        ];

        $pointType = 0;
        // 댓글인 경우
        if($newWrite->is_comment == 1) {
            // $toUpdateColumn = [
            //     'parent' => 원글의 글번호,
            //     'hit' => 0
            // ];
            $relAction = '댓글';
            $content = $this->board->subject . ' ' . '원글-' . $lastInsertId . ' 댓글쓰기';
            $pointType = $this->board->comment_point;
        } else {
            $relAction = '쓰기';
            $content = $this->board->subject . ' ' . $lastInsertId . ' 글쓰기';
            $pointType = $this->board->write_point;
        }
        // 포인트 부여(글쓰기, 댓글)
        $this->point->insertPoint($userId, $pointType, $content, $this->board->table_name, $lastInsertId, $relAction);

        if($request->has('notice')) {
            $this->registerNotice($lastInsertId);
        }

        // 댓글 or 답변글일 경우 원글의 num을 가져와서 넣는다.
        // if( (isset($inputData['is_comment']) && $inputData['is_comment'] == 1)
        //     || (isset($inputData['reply']) && $inputData['reply'] != '') ) {
        //     ;
        // }

        // 답변글일 경우 reply에 댓글 레벨을 표시한다. (inputData에는 원글의 reply 받아오기)
        // reply 구하는 공식 적용

        // 댓글일 경우 원글의 last에 최근 댓글 달린 시간을 업데이트한다.

        $writeModel->where('id', $lastInsertId)->update($toUpdateColumn);

        // 저장한 글이 임시저장을 사용한 것이라면 삭제한다.
        Autosave::where('unique_id', $request->uid)->delete();

        return $lastInsertId;
    }

    // 글 수정
    public function updateWrites($writeModel, $request, $writeId, $file)
    {
        $write = $writeModel->find($writeId);
        $user = Auth::user();
        $inputData = $request->all();
        $inputData = array_except($inputData, ['_method', '_token', 'file_del', 'file_content', 'attach_file',
                                                'html', 'secret', 'mail', 'notice', 'uid']);
        $inputData = $this->convertSomeField($inputData);

        $options = [];
        $options['html'] = $request->has('html') ? $request->html : '';
        $options['secret'] = $request->has('secret') ? $request->secret : '';
        $options['mail'] = $request->has('mail') ? $request->mail : '';

        foreach($options as $key => $value) {
            if($value == '') {
                $options = array_except($options, [$key]);
            }
        }

        $inputData = array_collapse([
            $inputData,
            [
                'ip' => $request->ip(),
                'option' => count($options) > 0 ? implode(',', $options) : null,
                'updated_at' => Carbon::now(),
                'file' => $file,
                // 'file' => count($request->attach_file),
            ]
        ]);
        // 비회원이거나 본인 글을 수정하는 것이 아닐 때
        if( is_null($user) || $write->user_id != $user->id) {
            $inputData = array_collapse([
                $inputData,
                [
                    'name' => $request->name,
                    'email' => $request->email,
                    'homepage' => $request->homepage,
                    'password' => $request->password!='' ? bcrypt($request->password) : $write->password,
                    'file' => $file,
                ]
            ]);
        }

        // 공지사항인 경우 boards에 등록하기
        $this->registerAndDeleteNotice($request, $writeId);

        // 저장한 글이 임시저장을 사용한 것이라면 삭제한다.
        Autosave::where('unique_id', $request->uid)->delete();

        $writeModel->where('id', $writeId)->update($inputData);

        return $writeModel->find($writeId);
    }

    // 공지사항 등록하기
    private function registerAndDeleteNotice($request, $writeId)
    {
        if($request->has('notice')) {
            $this->registerNotice($writeId);
        } else {
            $this->deleteNotice($writeId);
        }
    }

    // 공지사항 등록
    private function registerNotice($writeId) {
       $notice = $this->board->notice;
       $notices = explode(',', $notice);
       if(count($notices)>0) {
           if(!array_search($writeId, $notices) ) {
               array_push($notices, $writeId);
               // 오름차순으로 정렬
               $notices = array_sort($notices, function ($key, $value) {
                   return $key;
               });

               $notice = implode(',', $notices);
           }
       } else {
           $notice = $writeId;
       }

       $this->board->update(['notice' => $notice]);
   }

   // 공지사항 해제
   private function deleteNotice($writeId)
   {
       $notices = $this->board->notice;
       if($notices != '') {
           $noticeArr = explode(',', $notices);
           if (($key = array_search($writeId, $noticeArr)) !== false) {
               unset($noticeArr[$key]);
           }
           $notices = null;
           if(count($noticeArr) > 0) {
               $notices = implode(',', $noticeArr);
           }
           $this->board->update(['notice' => $notices]);
       }
   }

    // 몇 가지 필드 값 교체
    private function convertSomeField($inputData)
    {
        // 제목
        $subject = substr(trim($inputData['subject']),0,255);
        $inputData['subject'] = preg_replace("#[\\\]+$#", "", $subject);
        // 내용
        $content = substr(trim($inputData['content']),0,65536);
        $inputData['content'] = preg_replace("#[\\\]+$#", "", $content);
        // 링크1
        if (isset($inputData['link1'])) {
            $link1 = substr($inputData['link1'],0,1000);
            $link1 = trim(strip_tags($link1));
            $inputData['link1'] = preg_replace("#[\\\]+$#", "", $link1);
        }
        // 링크2
        if (isset($inputData['link2'])) {
            $link2 = substr($inputData['link2'],0,1000);
            $link2 = trim(strip_tags($link2));
            $inputData['link2'] = preg_replace("#[\\\]+$#", "", $link2);
        }

        return $inputData;
    }

    // (게시판) 게시물 복사, 게시물 이동 = 복사 + 기존 테이블에서 삭제
    // $writeModel : 원본 게시물 데이터 모델
    // $writeIds : 복사할 대상 게시물들의 id
    // $boardIds : 선택한 대상 게시판들의 id
    public function copyWrites($writeModel, $request)
    {
        $writeIds = session()->get('writeIds');
        $boardIds = $request->chk_id;

        // 복사할 대상 게시물들
        if(gettype($writeIds) == 'string') {
            $originalWrites = $writeModel->where('id', $writeIds)->get()->toArray();
        } else {
            $originalWrites = $writeModel->whereIn('id', $writeIds)->get()->toArray();
        }
        // 선택한 대상 게시판들
        $boards = Board::whereIn('id', $boardIds)->get();

        $message = '';
        if( !is_null($boards) ) {
            foreach($boards as $board) {
                // 게시판 테이블 셋팅
                // $destinationWrite : 복사되서 게시물이 추가되는 게시판
                $destinationWrite = new Write($board->table_name);
                $destinationWrite->setTableName($board->table_name);
                // num의 최소값
                $minNum = is_null($destinationWrite->min('num')) ? 0 : $destinationWrite->min('num');

                // $originalWrites : 복사할 원본 글들
                // 댓글도 함께 복사 처리가 추가 되야 함
                foreach($originalWrites as $write) {
                    // 복사할 글을 복사한 테이블에 맞춰서 num 재설정
                    $destinationWrite->insert(array_except($write, 'id'));  // 새로 insert하기 때문에 auto increment 되는 id값은 제거

                    // 복사할 글을 복사한 테이블에 맞춰서 parent 재설정
                    $lastInsertId = DB::getPdo()->lastInsertId();   // 마지막에 삽입한 행의 id 값 가져오기
                    $newWrite = $destinationWrite->where('id', $lastInsertId)->first();
                    $toUpdateColumn = [
                        'num' => $minNum,
                        'parent' => $newWrite->id,
                    ];
                    if($newWrite->reply != '') {
                        $toUpdateColumn['num'] = $destinationWrite->find($newWrite->id-1)->num;
                    } else {
                        $toUpdateColumn['num'] = --$minNum;
                    }

                    $destinationWrite->where('id', $lastInsertId)->update($toUpdateColumn);
                    // 복사할 때 원본 게시물에 첨부 파일이 있다면 board_files 테이블에 동일한 파일을 링크하는 정보를 추가해준다.
                    // 게시물 이동할 때는 board_files 테이블의 board_id와 write_id를 update(실제로는 row의 insert -> delete)한다.
                    if($write['file'] > 0 ) {
                        $this->updateAttachedFileInfo($writeModel, $write, $lastInsertId, $board, $request);
                    }

                    $message = '게시물 복사가 완료되었습니다.';
                }
            }
            // (게시물 이동) 원래 있던 곳의 테이블에서 해당 게시물 삭제
            if($request->type == 'move') {
                $message = $this->deleteCopyWrites($writeModel);
            }

        } else {
            $message = '게시물 복사에 실패하였습니다.';
        }

        return $message;
     }

     // 게시물 복사할 때 첨부파일정보도 함께 복사하는 메서드
     public function updateAttachedFileInfo($writeModel, $write, $lastInsertId, $toBoard, $request)
     {
         $boardFiles = BoardFile::where([
             'board_id' => $this->board->id,
             'write_id' => $write['id']
         ])->get();

         foreach($boardFiles as $boardFile) {
             $copyBoardFile = $boardFile->attributes;
             $copyBoardFile['write_id'] = $lastInsertId;
             $copyBoardFile['board_id'] = $toBoard->id;

             BoardFile::insert($copyBoardFile);
             // 파일복사(다른 테이블로 복사했을 경우)
             if($this->board->id != $toBoard->id) {
                 $this->copyFile($boardFile, $toBoard);
             }

             // 게시물 이동 시 기존 BoardFile 데이터 삭제.
             if($request->type == 'move') {
                 BoardFile::where([
                     'board_id' => $this->board->id,
                     'write_id' => $write['id'],
                     'board_file_no' => $boardFile->board_file_no
                 ])->delete();
             }
         }
     }

     // 파일 시스템에서 파일 복사
     public function copyFile($boardFile, $toBoard)
     {
         $from = storage_path('app/public/'. $this->board->table_name. '/'. $boardFile->file);
         $toDirectory = storage_path('app/public/'. Board::find($toBoard->id)->table_name);
         $to = $toDirectory. '/'. $boardFile->file;
         if( !File::exists($toDirectory) ) {
             File::makeDirectory($toDirectory);
         }
         File::copy($from, $to);
     }

     // 글 보기 -> 삭제
     public function deletePoint($writeModel, $writeId)
     {
         $write = $writeModel->find($writeId);
         // 원글에서의 처리
         $deleteResult = 0;
         $insertResult = 0;
         if(!$write->is_comment) {
             // 포인트 삭제 및 사용 포인트 다시 부여
             $deleteResult = $this->point->deletePoint($write->user_id, $this->board->table_name, $writeId, '쓰기');
             if($deleteResult == 0) {
                 $insertResult = $this->point->insertPoint($write->user_id, $this->board->write_point * (-1), $this->board->subject. ' '. $writeId. ' 글삭제');
             }
         } else {   // 댓글에서의 처리

         }

         return $deleteResult != 0 ? $deleteResult : $insertResult;
     }

    public function deleteWrite($writeModel, $writeId)
    {
        // 게시글 삭제
        $result = $writeModel->where('id', $writeId)->delete();

        // 최근 게시물

        // 스크랩 삭제

        // 공지사항 삭제해서 업데이트
        $this->deleteNotice($writeId);

        return $result;
     }

     // (게시판) 글 선택 삭제 - 글 갯수 만큼 deleteWrite() 메서드 돌리기
     public function selectDeleteWrites($writeModel, $ids)
     {
         // 첨부파일도 함께 삭제한다.
         $idsArr = explode(',', $ids);
         foreach($idsArr as $id) {
             // deleteWrite() 메서드 호출

             BoardFile::where([
                 'board_id' => $this->board->id,
                 'write_id' => $id
             ])->delete();
         }

         $result = $writeModel->whereRaw('id in (' . $ids . ') ')->delete();

         if($result > 0) {
             return '선택한 글이 삭제되었습니다.';
         } else {
             return '선택한 글의 삭제가 실패하였습니다.';
         }
     }

     // (게시물 이동) 기존 원본 게시물만 삭제 (첨부파일 정보변경은 updateAttachedFileInfo() 에서 한다.)
     public function deleteCopyWrites($writeModel)
     {
         $writeIds = session()->get('writeIds'); // 복사할 대상 게시물들의 id

         if(gettype($writeIds) == 'string') {
             $result = $writeModel->where('id', $writeIds)->delete();
         } else {
             // foreach로 deleteWrite() 메서드 호출

             $result = $writeModel->whereRaw('id in (' . implode(",", $writeIds) . ') ')->delete();
         }

         if($result > 0) {
             return '게시물 이동에 성공하였습니다.';
         } else {
             return '게시물 이동이 실패하였습니다.';
         }
     }

}
