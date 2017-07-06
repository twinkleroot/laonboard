<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;
use DB;
use File;
use Cache;
use Carbon\Carbon;
use Exception;
use App\User;
use App\Board;
use App\Point;
use App\Scrap;
use App\Common\Util;
use App\Common\CustomPaginator;
use App\Autosave;
use App\BoardNew;
use App\BoardFile;


class Write extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $appends = ['isReply', 'isEdit', 'isDelete'];

    protected $table;
    public $board;
    public $point;

    public $isReply;
    public $isEdit;
    public $isDelete;

    public function getIsReplyAttribute() {
        return $this->isReply;
    }

    public function getIsEditAttribute() {
        return $this->isEdit;
    }

    public function getIsDeleteAttribute() {
        return $this->isDelete;
    }

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // (게시판) index 페이지에서 필요한 파라미터 가져오기
    public function getIndexParams($writeModel, $request)
    {
        // 글 목록에서 글 보기로 넘어갈 때 가지고 있어야 할 파라미터를 가지는 배열
        $viewParams = [];

        // 전체 카테고리 리스트
        $categories = [];
        if($this->board->use_category && !is_null($this->board->category_list) ) {
            $categories = explode('|', $this->board->category_list);
            $categories = array_map('trim', $categories);
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
            $writes = $this->getWrites($writeModel, $request, $kind, $keyword, $currenctCategory);

            if($writes->currentPage() > 1) {
                $viewParams['page'] = 'page='. $writes->currentPage();
            }
        } catch (Exception $e) {
            return [ 'message' => $e->getMessage() ];
        }

        return [
            'board' => $this->board,
            'writes' => $writes,
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
            $parentWrite = $write;
            // 댓글일 경우 부모글의 제목을 댓글의 제목으로 넣기
            if($write->is_comment) {
                $parentWrite = $writeModel->where('id', $write->parent)->first();
                $write->subject = $parentWrite->subject;
            }
        }
        // 페이징 버튼의 경로 지정 (항상 목록으로 이동하도록 하기)
        $writes->withPath('/board/'.$this->board->id);

        return $writes;
    }

    // (게시판 리스트) select ~ from ~ where까지 얻어온다.
    public function getWritesWhere($writeModel, $kind, $keyword, $currenctCategory)
    {
        // 기본 ( 공지는 기본만 가져간다. )
        $query = $writeModel
                ->selectRaw($writeModel->table.'.*, users.level as user_level, users.id_hashKey as user_id_hashKey')
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
                for($i=0; $i<count($kinds); $i++) {
                    if (preg_match("/[a-zA-Z]/", $keyword)) {
                        $whereStr = "INSTR(LOWER($kinds[$i]), LOWER('$keyword'))";
                    } else {
                        $whereStr = "INSTR($kinds[$i], '$keyword')";
                    }

                    if($i == 0) {
                        $query = $query->whereRaw($whereStr);
                    } else {
                        $query = $query->orWhereRaw($whereStr);
                    }
                }
            // 코멘트 검색이 select box에 있는 경우
            } else if(str_contains($kind, ',')) {
                $kinds = explode(',', preg_replace("/\s+/", "", $kind));
                $user = User::where($kinds[0], $keyword)->first();
                // 검색 쿼리 붙이기
                if($user) {
                    $query = $query->where('user_id', $user->id)
                                   ->where('is_comment', $kinds[1]);
                } else {
                    throw new Exception($keyword. ' 사용자가 존재하지 않습니다.');
                }
            } else if($kind == 'name') {
                $query = $query->where($writeModel->table.'.name', $keyword);
            // 단독 키워드 검색(제목, 내용)
            } else {
                // $query = $query->where($kind, 'like', '%'.$keyword.'%');
                $query = $query->whereRaw("INSTR($kind, '$keyword')");
            }
        } else {
            $query = $query->where('is_comment', 0);
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

        $notices = explode(',', trim($this->board->notice));
        $notices = array_filter($notices);

        // 컬렉션 분할 (공지 + 그 외)
        // 공지 게시물들
        $noticeWrites = collect();
        $noticeWrites = $totalWrites->filter(function ($value, $key) use($notices) {
            return in_array($value->id, $notices);
        });
        // 그 외 게시물들
        $filteredWrites = collect();
        $filteredWrites = $totalWrites->reject(function ($value, $key) use($notices) {
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
        $notices = explode(',', trim($this->board->notice));
        $notices = array_filter($notices);

        return count($notices) > 0 ? true : false;
    }

    public function getViewParams($request, $boardId, $writeId, $writeModel)
    {
        $write = $writeModel->find($writeId);

        try {
            // 조회수 증가, 포인트 부여
            $write->hit = $this->beforeRead($write, $request);
        } catch (Exception $e) {
            return [ 'message' => $e->getMessage() ];
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

        // 검색어 색깔 다르게 표시
        if($request->has('keyword')) {
            $write->content = Util::searchKeyword($request->keyword, $write->content);
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
        $boardConfig = Cache::get("config.board");
        $imgExtension = $boardConfig->imageExtension;
        $boardFiles = [];
        $imgFiles = [];
        if($write->file > 0) {
            $boardFiles = BoardFile::where(['board_id' => $boardId, 'write_id' => $writeId])->get();

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

        // 에디터로 업로드한 이미지 경로를 추출해서 내용의 img 태그 부 분을 교체한다.
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

    // 이전 글, 다음 글 경로, 제목 가져오기
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
        $query = $this->getWritesWhere($writeModel, $kind, $keyword, $currenctCategory);
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
        $query = $this->getWritesWhere($writeModel, $kind, $keyword, $currenctCategory);
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

        // 구한 이전 글 정보로 이전 글 url, 제목을 얻는다.
        if(!$prevWrite) {
            $prevUrl = '';
            $prevSubject = '';
        } else {
            $prevUrl = $this->getPrevNextUrl($boardId, $request, $prevWrite);
            $prevSubject = $prevWrite['subject'];
        }

        // 구한 다음 글 정보로 다음 글 url, 제목을 얻는다.
        if(!$nextWrite) {
            $nextUrl = '';
            $nextSubject = '';
        } else {
            $nextUrl = $this->getPrevNextUrl($boardId, $request, $nextWrite);
            $nextSubject = $nextWrite['subject'];
        }

        return [
            'prevUrl' => $prevUrl,
            'nextUrl' => $nextUrl,
            'prevSubject' => $prevSubject,
            'nextSubject' => $nextSubject
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
        $hit = $write->hit;
        $user = auth()->user();
        $userId = !$user ? 0 : $user->id;
        $userHash = !$user ? '' : $user->id_hashkey;
        $sessionName = "session_view_". $this->board->table_name. '_'. $write->id. '_'. $userHash;
        if(!session()->get($sessionName) && $userId != $write->user_id) {
            // 조회수 증가
            $hit = $this->increaseHit($write);
            // 포인트 계산(차감)
            $message = $this->calculatePoint($write, $request, 'read');

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
        $userId = !$user ? 0 : $user->id;
        $userPoint = !$user ? 0 : $user->point;
        if($write->user_id > 0 && $write->user_id == $userId) {
            ;
        } else if(!$user && $boardlevel == 1 && $write->ip == $request->ip()) {
            ;
        } else {
            // 포인트 사용 && 소모되는 포인트가 있는지 && 현재 사용자가 갖고 있는 포인트로 사용 가능한지 검사
            if (Cache::get("config.homepage")->usePoint && $boardPoint && $userPoint + $boardPoint < 0) {
                $message = '보유하신 포인트('.number_format($userPoint).')가 없거나 모자라서'. $contentPiece. '('.number_format($boardPoint).')가 불가합니다.\\n\\n포인트를 적립하신 후 다시'.$contentPiece.' 해 주십시오.';
                throw new Exception($message);
            }
            // 포인트 부여(글 읽기, 파일 다운로드)
            $this->point->insertPoint($userId, $boardPoint,
                $this->board->subject . ' ' . $write->id . $contentPiece, $this->board->table_name, $write->id, $action);
        }
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

        $write = $writeModel->find($writeId);
        $user = auth()->user();
        $userId = is_null($user) ? 0 : $user->id;
        $sessionName = 'session_download_'. $this->board->table_name. '_'. $write->id. '_'. $fileNo;
        if(session()->get('admin') || $userId == $write->user_id) {   // 관리자나 작성자 본인이면 패스
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

    // (게시판) 글 쓰기 폼
    public function getCreateParams($writeModel, $request)
    {
        $board = $this->board;
        $categories = [];
        if( !is_null($board->category_list) ) {
            $categories = explode('|', $board->category_list);
            $categories = array_map('trim', $categories);
        }

        $autosaveCount = 0;
        if(auth()->user()) {
            $autosaveCount = Autosave::getAutosaveCount();
        }

        return [
            'type' => 'create',
            'board' => $board,
            'currenctCategory' => $request->category ? : '',
            'categories' => $categories,
            'autosaveCount' => $autosaveCount,
        ];
    }

    // 글 수정 폼
    public function getEditParams($boardId, $writeId, $writeModel, $request)
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

        // 파일첨부 칸이 최소한 환경설정에서 설정한 대로 나올 수 있도록 file 값을 조정한다.
        $uploadedFileCount = $write->file;
        $configUploadFileCount = $this->board->upload_count;
        $write->file = $uploadedFileCount < $configUploadFileCount ? $configUploadFileCount : $uploadedFileCount;

        // 글쓰기와 같은 폼을 쓰기때문에 글 쓰기할 때 가져왔던 파라미터를 가져온다.
        $createParams = $this->getCreateParams($writeModel, $request);
        $createParams['type'] = 'update';

        $params = [
            'write' => $write,
            'boardFiles' => $boardFiles,
        ];

        $params = array_collapse([$params, $createParams]);

        return $params;
    }

    // 답변 글 폼
    public function getReplyParams($boardId, $writeId, $writeModel, $request)
    {
        $write = $writeModel->find($writeId);
        // 글쓰기와 같은 폼을 쓰기때문에 글 쓰기할 때 가져왔던 파라미터를 가져온다.
        $createParams = $this->getCreateParams($writeModel, $request);
        $createParams['type'] = 'reply';

        $write->subject = 'Re: '. $write->subject;

        $params = [
            'write' => $write,
        ];

        $params = array_collapse([$params, $createParams]);

        return $params;
    }

    // (게시판) 글 쓰기 -> 저장
    public function storeWrite($writeModel, $request)
    {
        $inputData = $request->all();
        $inputData = array_except($inputData, ['_token', 'file_content', 'attach_file', 'html',
                                                'secret', 'mail', 'notice', 'uid', 'type', 'writeId', 'g-recaptcha-response']);
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
        $userId = 0;    // $userId가 1이면 비회원
        $name = '';
        $password = '';
        $minNum = $writeModel->min('num');
        $num = $minNum - 1;
        $email = '';
        $homepage = '';
        $reply = '';

        // 글 답변일 때 num과 reply 값 변경
        if($request->type == 'reply') {

            $write = $writeModel->find($request->writeId);

            if($request->has('secret')) {
                $password = $write->password;
            }
            $num = $write->num;
            $reply = $this->getReplyValue($writeModel, $write);
            if(!preg_match("/^[A-Z]+$/", $reply)) {
                return ['message' => $reply];
            }
        }

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
                'hit' => 1,
                'num' => $num,
                'reply' => $reply,
            ]
        ]);

        $writeModel->insert($insertData);
        $lastInsertId = DB::getPdo()->lastInsertId();   // 마지막에 삽입한 행의 id 값 가져오기
        $newWrite = $writeModel->where('id', $lastInsertId)->first();

        // 포인트 부여(글쓰기, 댓글)
        $pointType = 0;
        $relAction = '쓰기';
        $content = $this->board->subject . ' ' . $lastInsertId;
        if($request->type == 'reply') {
            $content .= ' 글답변';
            $pointType = $this->board->comment_point;
        } else {
            $content .= ' 글쓰기';
            $pointType = $this->board->write_point;
        }
        $this->point->insertPoint($userId, $pointType, $content, $this->board->table_name, $lastInsertId, $relAction);

        // 공지사항인 경우 등록
        if($request->has('notice')) {
            $this->registerNotice($lastInsertId);
        }

        $writeModel->where('id', $lastInsertId)->update(['parent' => $newWrite->id]);

        // 새글 Insert
        BoardNew::Create([
            'board_id' => $this->board->id,
            'write_id' => $lastInsertId,
            'write_parent' => $lastInsertId,
            'created_at' => Carbon::now(),
            'user_id' => $userId
        ]);

        // 댓글 1 증가
        $this->board->update(['count_write' => $this->board->count_write + 1]);

        // 저장한 글이 임시저장을 사용한 것이라면 삭제한다.
        Autosave::where('unique_id', $request->uid)->delete();

        // 메인 최신글 캐시 삭제
        Util::deleteCache('main', $this->board->table_name);

        return $lastInsertId;
    }

    // 답변 글 단계 구하는 로직
    private function getReplyValue($writeModel, $write)
    {
        $replyLength = strlen($write->reply) + 1;
        if ($this->board->reply_order == 1) {
            $baginReplyChar = 'A';
            $endReplyChar = 'Z';
            $replyNumber = 1;
            $query = $writeModel->selectRaw("MAX(SUBSTRING(reply, ". $replyLength. ", 1)) as reply")
                    ->where('num', $write->num)
                    ->whereRaw("SUBSTRING(reply, ". $replyLength. ", 1) <> ''");
        } else {
            $baginReplyChar = 'Z';
            $endReplyChar = 'A';
            $replyNumber = -1;
            $query = $writeModel->selectRaw("MIN(SUBSTRING(reply, ". $replyLength. ", 1)) as reply")
                    ->where('num', $write->num)
                    ->whereRaw("SUBSTRING(reply, ". $replyLength. ", 1) <> ''");

        }
        if ($write->reply) {
            $query->where('reply', 'like', $write->reply.'%');
        }
        $result = $query->first(); // 쿼리 실행 결과

        if (is_null($result->reply)) {
            $replyChar = $baginReplyChar;
        } else if ($result->reply == $endReplyChar) { // A~Z은 26 입니다.
            return '더 이상 답변하실 수 없습니다.\\n답변은 26개 까지만 가능합니다.';
        } else {
            $replyChar = chr(ord($result->reply) + $replyNumber);
        }

        if(is_null($write->reply)) {
            $write->reply = '';
        }
        $reply = $write->reply . $replyChar;

        return $reply;
    }

    // 글 수정
    public function updateWrite($writeModel, $request, $writeId, $file)
    {
        $write = $writeModel->find($writeId);
        $user = Auth::user();
        $inputData = $request->all();
        $inputData = array_except($inputData, ['_method', '_token', 'file_del', 'file_content', 'attach_file',
                                                'html', 'secret', 'mail', 'notice', 'uid', 'type', 'writeId']);
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

        // 기존 content의 img 태그의 파일을 추출하고 수정된 content의 content를 비교해서 없어진 파일은 서버에서 삭제한다.
        $this->deleteEditorImage($write->content, $inputData['content']);

        // 글 수정 실행
        $writeModel->where('id', $writeId)->update($inputData);

        // 메인 최신글 캐시 삭제
        Util::deleteCache('main', $this->board->table_name);

        return $writeModel->find($writeId);
    }

    // 에디터 첨부 이미지를 수정 전과 후를 비교해서 지운 이미지 파일 서버에서 삭제
    private function deleteEditorImage($originalContent, $editContent)
    {
        $originalContentImages = $this->getImageNameByContent($originalContent);
        $EditContentImages = $this->getImageNameByContent($editContent);

        foreach($originalContentImages as $originalContentImage) {
            $img = str_replace("thumb-", "", $originalContentImage);
            if( array_search($img, $EditContentImages) === false) {
                // 서버에서 파일 삭제
                $boardFile = new BoardFile();
                $boardFile->deleteFileOnServer('editor', $img);
            }
        }
    }

    // 에디터로 업로드한 이미지 경로를 추출한다.
    private function getImageNameByContent($content)
    {
        $pattern = "/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i";
        preg_match_all($pattern, $content, $matches);

        $imageName = array();
        for($i=0; $i<count($matches[1]); $i++) {
            // 이미지 파일만 추출해서 배열에 담는다.
            array_push($imageName, basename($matches[1][$i]));
        }

        return $imageName;
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

    // 해당 글에 답변글이 달려 있는지 확인한다.
    public function hasReply($writeModel, $writeId)
    {
        $write = $writeModel->find($writeId);
        $replyCount = $writeModel->where('reply', 'like', $write->reply.'%')
                        ->where('id', '<>', $writeId)
                        ->where(['num' => $write->num, 'is_comment' => 0])
                        ->count('id');
        if($replyCount > 0 && !session()->get('admin')) {
            return true;
        }

        return false;
    }

    // 해당 글에 댓글이 달려 있는지 확인한다.
    public function hasComment($writeModel, $writeId)
    {
        $user = auth()->user();
        $userId = is_null($user) ? 0 : $user->id;
        $commentCount = $writeModel->where('user_id', '<>', $userId)
                        ->where(['parent' => $writeId, 'is_comment' => 1])
                        ->count('id');
        if($commentCount >= $this->board->count_delete && !session()->get('admin')) {
            return true;
        }
        return false;
    }

    // 글 삭제 - 게시글 삭제
    public function deleteWrite($writeModel, $writeId)
    {

       $write = $writeModel->find($writeId);
       // 원글에 달린 댓글
       $comments = $writeModel->select('id')->where([
           'is_comment' => 1,
           'num' => $write->num
       ])->get()->toArray();
       // 댓글쓰기에 부여된 포인트 삭제
       foreach($comments as $comment) {
           // 포인트 삭제 및 사용 포인트 다시 부여
           $comment = $writeModel->find($comment['id']);
           $deleteResult = $this->point->deletePoint($comment->user_id, $this->board->table_name, $comment->id, '댓글');
           if($deleteResult == 0) {
               $insertResult = $this->point->insertPoint($comment->user_id, $this->board->write_point * (-1), $this->board->subject. ' '. $comment->parent. '-'. $comment->id. ' 댓글삭제');
           }
       }

       // 게시글 삭제(댓글)
       $result = $writeModel->where(['num' => $write->num, 'reply' => $write->reply])->delete();

       // 삭제한 게시물 갯수만큼 총 게시글 갯수에서 차감하기
       $this->board->update(['count_write' => $this->board->count_write - $result]);

       // 새글 삭제
       BoardNew::where([
           'board_id' => $this->board->id,
           'write_parent' => $writeId
       ])->delete();

       // 스크랩 삭제
       Scrap::where([
          'board_id' => $this->board->id,
          'write_id' => $writeId
       ])->delete();

       // 공지사항 삭제해서 업데이트
       $this->deleteNotice($writeId);

       // 메인 최신글 캐시 삭제
       Util::deleteCache('main', $this->board->table_name);

       return $result;
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

}
