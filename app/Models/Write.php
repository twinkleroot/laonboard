<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Contracts\WriteInterface;
use Carbon\Carbon;
use Auth;
use Cache;
use DB;
use Exception;
use File;

class Write extends Model implements WriteInterface
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $appends = ['isReply', 'isEdit', 'isDelete'];

    protected $table;

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

    public static function getWrite($boardId, $writeId, $id='id')
    {
        static $write;
        if (is_null($write) || $write[$id] != $writeId || $write->board_id != $boardId) {
            $writeModel = app()->tagged('write')[0];    // 컨테이너에 미리 구현체를 주입한 Write 객체를 가져옴
            $board = Board::getBoard($boardId);
            $writeModel->setTableName($board->table_name);
            $write = $writeModel->find($writeId);
            if($write) {
                $write->board_id = $board->id;
            }
        }

        return $write;
    }

    // write 모델의 테이블 이름을 지정
    public function setTableName($tableName)
    {
        $this->table = 'write_'. $tableName;
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
        if(notNullCount($queryStr) > 1) {
            $currenctCategory = explode('&', $queryStr[1])[0];
            $viewParams['category'] = 'category='. $currenctCategory;
        }

        // 검색 기준
        $kind = '';
        if($request->filled('kind')) {
            $kind = $request->kind;
            $viewParams['kind'] = 'kind='. $kind;
        }

        // 검색어
        $keyword = '';
        if($request->filled('keyword')) {
            $keyword = $request->keyword;
            $viewParams['keyword'] = 'keyword='. $keyword;
        }

        $userLevel = auth()->check() ? auth()->user()->level : 1;
        $notices = explode(',', $this->board->notice);

        $writes = $this->getWrites($writeModel, $request, $kind, $keyword, $currenctCategory);

        if($writes->currentPage() > 1) {
            $viewParams['page'] = 'page='. $writes->currentPage();
        }

        fireEvent('afterSearch');

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
            'search' => $request->filled('keyword') ? 1 : 0,
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
        $hasNotice = $this->hasNotice();

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
        // 3. 게시판 설정에 따라 목록에서 보이는 제목을 표시하고 나머지는 ...로 표시한다.
        foreach($writes as $write) {
            $notHasheduserId = $write->user_id;
            $write->level = $write->user_level;
            $write->user_id = $write->user_id_hashkey;     // 라라벨 기본 지원 encrypt
            $write->subject = searchKeyword($keyword, $write->subject);
            $write->subject = subjectLength($write->subject, $this->board->subject_len);
            $parentWrite = $write;
            // 댓글일 경우 부모글의 제목을 댓글의 제목으로 넣기
            if($write->is_comment) {
                $parentWrite = $writeModel->where('id', $write->parent)->first();
                $write->subject = $parentWrite->subject;
            }

            if($this->board->skin == 'gallery') {
                $write->listThumbnailPath = $this->getListThumbnail($write);
            }

            if($notHasheduserId && cache('config.join')->useMemberIcon) {
                $folder = getIconFolderName($write->user_created_at);
                $iconName = getIconName($notHasheduserId, $write->user_created_at);
                $iconPath = storage_path('app/public/user/'. $folder. '/'). $iconName. '.gif';
                $write->iconPath = '';
                if(File::exists($iconPath)) {
                    $write->iconPath = '/storage/user/'. $folder. '/'. $iconName. '.gif';
                }
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
                ->select($writeModel->getTable().'.*', 'users.level as user_level', 'users.id_hashkey as user_id_hashkey', 'users.created_at as user_created_at')
                ->leftJoin('users', 'users.id', '=', $writeModel->getTable().'.user_id');

        // + 카테고리
        if($currenctCategory != '') {
            $query = $query->where('ca_name', $currenctCategory);
        }

        // + [카테고리] + 검색
        if($kind != '' && $keyword != '') {
            if($kind == 'user_id') {
                // 암호화된 user_id를 복호화해서 검색한다.
                $user = User::where('id_hashkey', $keyword)->first();
                $userId = $user ? User::where('id_hashkey', $keyword)->first()->id : -1;
                $query = $query->where('user_id', $userId);
            } else if(str_contains($kind, '||')) { // 제목 + 내용으로 검색
                $kinds = explode('||', preg_replace("/\s+/", "", $kind));
                // 검색 쿼리 붙이기
                for($i=0; $i<notNullCount($kinds); $i++) {
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
                $query = $query->where($writeModel->table.'.name', $keyword);
                if($kinds[1] == 0) {	// 글쓴이 원글만 검색
                    $query = $query->where('is_comment', $kinds[1]);
                }
            } else if($kind == 'name') {
                $query = $query->where($writeModel->table.'.name', $keyword);
            } else { // 단독 키워드 검색(제목, 내용)
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

    // 글 목록 결과물에 공지사항이 있는지 검사한다.
    private function hasNotice()
    {
        $notices = explode(',', trim($this->board->notice));
        $notices = array_filter($notices);

        return notNullCount($notices) > 0 ? true : false;
    }

    // 수동 페이징
    public function customPaging($request, $query, $sortField)
    {
        $currentPage = $request->filled('page') ? $request->page : 1 ;
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

        $writes = new CustomPaginator($sliceWrites, notNullCount($mergeWrites), $this->board->page_rows, $currentPage);

        return $writes;
    }

    private function getListThumbnail($write)
    {
        $notices = explode(',', trim($this->board->notice));
        if(in_array($write->id, $notices)) {
            return '공지';
        }

        $imgExtension = cache("config.board")->imageExtension;
        $boardFiles = [];
        $imgFiles = [];
        $imageFileInfo = '';
        if($write->file > 0) {	// 첨부파일에 이미지가 있는지 검사해서 있으면 하나만 썸네일로 만들어서 가져온다.
            $boardFiles = BoardFile::where(['board_id' => $this->board->id, 'write_id' => $write->id])->get();

            foreach($boardFiles as $boardFile) {
                $filePiece = explode('.', $boardFile->file);
                if( !str_contains($imgExtension, last($filePiece))) {
                    continue;
                }
                // 이미지 경로를 가져와서 썸네일만든 후 서버에 저장
                $imageFileInfo = getViewThumbnail($this->board, $boardFile->file, $this->board->table_name, 'list');
                $imageFileInfo = array_add($imageFileInfo, 'path', '/storage/'. $this->board->table_name. '/'. $imageFileInfo['name']);
                break;
            }
        } else {	// 에디터로 작성한 내용에 이미지가 있는지 검사해서 있으면 하나만 썸네일로 만들어서 가져온다.
            preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $write->content, $matches);

            for($i=0; $i<notNullCount($matches[1]); $i++) {
                $imageFileInfo = getViewThumbnail($this->board, basename($matches[1][$i]), 'editor', 'list');
                $imageFileInfo = array_add($imageFileInfo, 'path', '/storage/editor/'. $imageFileInfo['name']);
                break;
            }
        }

        if($imageFileInfo) {
            return $imageFileInfo['path'];
        } else {
            return 'no image';
        }
    }

    public function getViewParams($writeModel, $writeId, $request)
    {
        $write = Write::getWrite($this->board->id, $writeId);

        // 글쓰기 할때 html 체크에 따라 글 내용 보여주는 방식을 다르게 한다.
        // html = 0 - 체크안함, 1 - 체크 후 취소, 2 - 체크 후 확인
        $html = 0;
        if (stripos($write->option, 'html1') !== false) {
            $html = 1;
        } else if (stripos($write->option, 'html2') !== false) {
            $html = 2;
        }

        $write->content = convertContent($write->content, $html);
        // 에디터로 업로드한 이미지 경로를 추출해서 내용의 img 태그 부분을 교체한다.
        $write->content = includeImagePathByEditor($this->board, $write->content);
        // 검색어 색깔 다르게 표시
        if($request->filled('keyword')) {
            $write->content = searchKeyword($request->keyword, $write->content);
        }

        // 관리자 여부에 따라 ip 다르게 보여주기
        if( auth()->guest() || !session()->get('admin') ) {
            if ($write->ip) {
                $write->ip = preg_replace("/([0-9]+).([0-9]+).([0-9]+).([0-9]+)/", config('laon.IP_DISPLAY'), $write->ip);
            }
        }

        $user = User::getUser($write->user_id);
        // 사용자 등급 추가
        $write->level = $user ? $user->level : 0;
        // 서명 사용하면 글쓴이의 서명을 담는다.
        $signature = '';
        if($this->board->use_signature && $write->user_id > 0) {
            $signature = $user->signature;
        }

        // 첨부 파일과 이미지 파일 분류
        $imgExtension = cache("config.board")->imageExtension;
        $boardFiles = [];
        $imgFiles = [];
        if($write->file > 0) {
            $boardFiles = BoardFile::where(['board_id' => $this->board->id, 'write_id' => $writeId])->get();

            foreach($boardFiles as $boardFile) {
                // 첨부파일이 이미지라면 업로드된 파일의 확장자를 가져와서
                // 게시판 기본설정에 설정한 업로드 가능한 이미지 확장자인지 검사하고
                // 이미지가 아니라면 통과시킨다.
                $filePiece = explode('.', $boardFile->file);
                if( !str_contains($imgExtension, last($filePiece))) {
                    continue;
                }
                // 이미지 경로를 가져와서 썸네일만든 후 서버에 저장
                $imageFileInfo = getViewThumbnail($this->board, $boardFile->file, $this->board->table_name);

                array_push($imgFiles, $imageFileInfo);
                // 이미지 파일은 파일 첨부 컬렉션에서는 제외
                $boardFiles = $boardFiles->reject(function ($value, $key) use ($boardFile) {
                    return $value->file == $boardFile->file;
                });
            }
        }

        // 글 제목 길이 설정에 따라 조정하기
        $write->subject = subjectLength($write->subject, $this->board->subject_len);

        if($write->user_id && cache('config.join')->useMemberIcon) {
            $folder = getIconFolderName($user->created_at);
            $iconName = getIconName($user->id, $user->created_at);
            $iconPath = storage_path('app/public/user/'. $folder. '/'). $iconName. '.gif';
            if(File::exists($iconPath)) {
                $write->iconPath = '/storage/user/'. $folder. '/'. $iconName. '.gif';
            }
        }

        $write->user_id = $user ? $user->id_hashkey : 0;

        $scrap = Scrap::where([
            'user_id' => auth()->check() ? auth()->user()->id : 0,
            'board_id' => $this->board->id,
            'write_id' => $write->id,
        ])->first();

        return [
            'board' => $this->board,
            'write' => $write,
            'scrap' => $scrap,
            'request' => $request,
            'signature' => $signature,
            'boardFiles' => $boardFiles,
            'imgFiles' => $imgFiles,
        ];
    }

    // 조회수 증가
    public function increaseHit($writeModel, $write)
    {
        $hit = $write->hit + 1;
        $writeModel->where('id', $write->id)->update(['hit' => $hit]);

        return $write->hit;
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
            if (cache("config.homepage")->usePoint && $boardPoint && $userPoint + $boardPoint < 0) {
                $message = '보유하신 포인트('.number_format($userPoint).')가 없거나 모자라서'. $contentPiece. '('.number_format($boardPoint).')가 불가합니다.\\n\\n포인트를 적립하신 후 다시'.$contentPiece.' 해 주십시오.';

                abort(500, $message);
            }
            // 포인트 부여(글 읽기, 파일 다운로드)
            insertPoint($userId, $boardPoint, $this->board->subject . ' ' . $write->id . $contentPiece, $this->board->table_name, $write->id, $action);
        }
    }

    // 이전 글, 다음 글 경로, 제목 가져오기
    public function getPrevNextView($writeModel, $writeId, $request)
    {
        $write = Write::getWrite($this->board->id, $writeId);
        // 파라미터 구하기
        $params = $request->query();

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
            $prevUrl = $this->getPrevNextUrl($request, $prevWrite);
            $prevSubject = $prevWrite['subject'];
        }

        // 구한 다음 글 정보로 다음 글 url, 제목을 얻는다.
        if(!$nextWrite) {
            $nextUrl = '';
            $nextSubject = '';
        } else {
            $nextUrl = $this->getPrevNextUrl($request, $nextWrite);
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
    public function getPrevNextUrl($request, $write)
    {
        $url = route('board.view', ['boardId' => $this->board->table_name, 'writeId' => $write->id ]);

        if($request->server('QUERY_STRING') != '') {
           $url .= '?'. $request->server('QUERY_STRING');
        }

        return $url;
    }

    // 글 읽기 중 링크 연결
    public function beforeLink($writeModel, $writeId, $linkNo)
    {
        $write = Write::getWrite($this->board->id, $writeId);
        $linkUrl = '';
        if(!$write['link'.$linkNo]) {
            abort(500, '링크가 없습니다.');
        }

        // 링크 연결수 증가
        $sessionName = 'session_link_'. $this->board->table_name. '_'. $write->id. '_'. $linkNo;
        if(!session()->get($sessionName)) {
            $this->increaseLinkHit($write, $linkNo);
            session()->put($sessionName, true);
        }

        // 글에 있는 링크를 링크 페이지로 넘김
        $linkUrl = $write['link'.$linkNo];

        return $linkUrl;
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

    // (게시판) 글 쓰기 폼
    public function getCreateParams($request)
    {
        $categories = [];

        if($this->board->category_list) {
            $categories = explode('|', $this->board->category_list);
            $categories = array_map('trim', $categories);
        }

        $autosaveCount = 0;

        if(auth()->user()) {
            $autosaveCount = Autosave::getAutosaveCount();
        }

        return [
            'type' => 'create',
            'board' => $this->board,
            'currenctCategory' => $request->category ? : '',
            'categories' => $categories,
            'autosaveCount' => $autosaveCount,
            'userLevel' => auth()->guest() ? 1 : auth()->user()->level,
        ];
    }

    // 글 수정 폼
    public function getEditParams($writeId, $writeModel, $request)
    {
        $write = Write::getWrite($this->board->id, $writeId);

        $boardFiles = [];
        if($write->file > 0) {
            $boardFiles = BoardFile::where([
                'board_id' => $this->board->id,
                'write_id' => $writeId,
            ])->get();
        }
        foreach($boardFiles as $file) {
            $file->filesize = getFileSize($file->filesize);
        }

        // 파일첨부 칸이 최소한 환경설정에서 설정한 대로 나올 수 있도록 file 값을 조정한다.
        $uploadedFileCount = $write->file;
        $configUploadFileCount = $this->board->upload_count;
        $write->file = $uploadedFileCount < $configUploadFileCount ? $configUploadFileCount : $uploadedFileCount;

        // 글쓰기와 같은 폼을 쓰기때문에 글 쓰기할 때 가져왔던 파라미터를 가져온다.
        $createParams = $this->getCreateParams($writeModel, $request);
        $createParams['type'] = 'update';

        if(!$this->board->use_dhtml_editor) {
            $write->content = strip_tags($write->content);
        }

        $params = [
            'write' => $write,
            'boardFiles' => $boardFiles,
        ];

        $params = array_collapse([$params, $createParams]);

        return $params;
    }

    // 답변 글 폼
    public function getReplyParams($writeId, $writeModel, $request)
    {
        $write = Write::getWrite($this->board->id, $writeId);
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
        $inputData = array_except($inputData, ['_token', 'file_content', 'attach_file', 'html', 'secret', 'mail', 'notice', 'uid', 'type', 'writeId', 'g-recaptcha-response']);
        $inputData = exceptNullData($inputData);
        $inputData = $this->convertSomeField($inputData);

        $options = [];
        $options['html'] = $request->filled('html') ? $request->html : '';
        $options['secret'] = $request->filled('secret') ? $request->secret : '';
        $options['mail'] = $request->filled('mail') ? $request->mail : '';

        if($options['html']) {
            $inputData['content'] = urlAutoLink($inputData['content']);
        }

        foreach($options as $key => $value) {
            if($value == '') {
                $options = array_except($options, [$key]);
            }
        }

        $user = auth()->user();
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
            $write = Write::getWrite($this->board->id, $request->writeId);

            if($request->filled('secret')) {
                $password = $write->password;
            }
            $num = $write->num;
            $reply = $this->getReplyValue($writeModel, $write);
        }

        // 회원 글쓰기 일 때
        if(auth()->check()) {
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
            $email = isset($inputData['email']) ? $inputData['email'] : null;
            $homepage = isset($inputData['homepage']) ? $inputData['homepage'] : null;
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
                'option' => notNullCount($options) > 0 ? implode(',', $options) : null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'file' => notNullCount($request->attach_file),
                'link1' => $request->link1 && !str_contains($request->link1, ['http://', 'https://']) ? 'http://'. $request->link1 : $request->link1,
                'link2' => $request->link2 && !str_contains($request->link2, ['http://', 'https://']) ? 'http://'. $request->link2 : $request->link2,
                'hit' => 1,
                'num' => $num,
                'reply' => $reply,
            ]
        ]);

        $lastInsertId = $writeModel->insertGetId($insertData);
        $newWrite = Write::getWrite($this->board->id, $lastInsertId);

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
        insertPoint($userId, $pointType, $content, $this->board->table_name, $lastInsertId, $relAction);

        // 공지사항인 경우 등록
        if($request->filled('notice')) {
            $this->registerNotice($lastInsertId);
        }

        $writeModel->where('id', $lastInsertId)->update(['parent' => $newWrite->id]);

        // 새글 Insert
        BoardNew::insert([
            'board_id' => $this->board->id,
            'write_id' => $lastInsertId,
            'write_parent' => $lastInsertId,
            'created_at' => Carbon::now(),
            'user_id' => $userId
        ]);

        // 원글 1 증가
        $this->board->update(['count_write' => $this->board->count_write + 1]);

        // 저장한 글이 임시저장을 사용한 것이라면 삭제한다.
        Autosave::where('unique_id', $request->uid)->delete();

        // 메인 최신글 캐시 삭제
        deleteCache('main', $this->board->table_name);

        // 비회원 글쓰기 + 비밀글일 경우 세션에 등록하기
        if(auth()->guest() && $request->filled('secret') && $request->secret) {
            session()->put(session()->getId(). 'secret_board_'. $this->board->table_name. '_write_'. $lastInsertId, true);
        }

        return $newWrite;
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
            abort(500, '더 이상 답변하실 수 없습니다.\\n답변은 26개 까지만 가능합니다.');
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
        $write = Write::getWrite($this->board->id, $writeId);
        $user = auth()->user();
        $inputData = $request->all();
        $inputData = array_except($inputData, ['_method', '_token', 'file_del', 'file_content', 'attach_file', 'g-recaptcha-response', 'html', 'secret', 'mail', 'notice', 'uid', 'type', 'writeId', 'queryString']);
        $inputData = $this->convertSomeField($inputData);

        $options = [];
        $options['html'] = $request->filled('html') ? $request->html : '';
        $options['secret'] = $request->filled('secret') ? $request->secret : '';
        $options['mail'] = $request->filled('mail') ? $request->mail : '';

        if($options['html']) {
            $inputData['content'] = urlAutoLink($inputData['content']);
        }

        foreach($options as $key => $value) {
            if($value == '') {
                $options = array_except($options, [$key]);
            }
        }

        $inputData = array_collapse([
            $inputData,
            [
                'ip' => $request->ip(),
                'option' => notNullCount($options) > 0 ? implode(',', $options) : null,
                'updated_at' => Carbon::now(),
                'file' => $file,
            ]
        ]);

        if($inputData['link1'] && $inputData['link1'] != $write->link1) {
            $inputData['link1'] = $inputData['link1'] && !str_contains($inputData['link1'], ['http://', 'https://']) ? 'http://'. $inputData['link1'] : $inputData['link1'];
            $inputData['link1_hit'] = 0;
        }
        if($inputData['link2'] && $inputData['link2'] != $write->link2) {
            $inputData['link2'] = $inputData['link2'] && !str_contains($inputData['link2'], ['http://', 'https://']) ? 'http://'. $inputData['link2'] : $inputData['link2'];
            $inputData['link2_hit'] = 0;
        }

        // 비회원이거나 본인 글을 수정하는 것이 아닐 때
        if( !auth()->check() || (!$user->isBoardAdmin($this->board) && $write->user_id != $user->id)) {
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
        $this->updateEditorImage($write->content, $inputData['content']);

        // 글 수정 실행
        $writeModel->where('id', $writeId)->update($inputData);

        // 메인 최신글 캐시 삭제
        deleteCache('main', $this->board->table_name);

        return Write::getWrite($this->board->id, $writeId);
    }

    // 에디터 첨부 이미지를 수정 전과 후를 비교해서 지운 이미지 파일 서버에서 삭제
    private function updateEditorImage($originalContent, $editContent)
    {
        $originalContentImages = $this->getImageNameByContent($originalContent);
        $EditContentImages = $this->getImageNameByContent($editContent);

        foreach($originalContentImages as $originalContentImage) {
            if(array_search($originalContentImage, $EditContentImages) === false) {	// 수정된 내용 안에 기존 업로드한 이미지가 없으면
                // 서버에서 파일 삭제
                $boardFile = new BoardFile();
                $boardFile->deleteFileOnServer($this->board, 'editor', $originalContentImage);
            }
        }
    }

    // 에디터로 업로드한 이미지 경로를 추출한다.
    private function getImageNameByContent($content)
    {
        $pattern = "/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i";
        preg_match_all($pattern, $content, $matches);

        $imageName = array();
        for($i=0; $i<notNullCount($matches[1]); $i++) {
            // 이미지 파일만 추출해서 배열에 담는다.
            array_push($imageName, basename($matches[1][$i]));
        }

        return $imageName;
    }

    // 공지사항 등록하기
    private function registerAndDeleteNotice($request, $writeId)
    {
        if($request->filled('notice')) {
            $this->registerNotice($writeId);
        } else {
            $this->deleteNotice($writeId);
        }
    }

    // 공지사항 등록
    private function registerNotice($writeId) {
       $notice = $this->board->notice;
       $notices = explode(',', $notice);
       if(notNullCount($notices)>0) {
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
           if(notNullCount($noticeArr) > 0) {
               $notices = implode(',', $noticeArr);
           }
           $this->board->update(['notice' => $notices]);
       }
    }

    // 게시글 삭제하면서 게시글에 종속된 것들도 함께 삭제
    public function deleteWriteCascade($writeModel, $writeId)
    {
        // 부여되었던 포인트 삭제 및 조정 반영
        $write = Write::getWrite($this->board->id, $writeId);
        if($write->user_id)  {
            deleteWritePoint($writeModel, $this->board->id, $writeId);
        }
        // 서버에서 첨부파일+첨부파일의 썸네일 삭제, 파일 테이블 삭제
        $boardFile = new BoardFile();
        $result = $boardFile->deleteWriteAndAttachFile($this->board->id, $writeId);
        if(!$result) {
            abort(500, '정상적으로 게시글을 삭제하는데 실패하였습니다.(첨부 파일 삭제)');
        }
        // 게시글 삭제
        $result = $this->deleteWrite($writeModel, $writeId);
        if($result <= 0) {
            abort(500, '정상적으로 게시글을 삭제하는데 실패하였습니다.(게시글 삭제)');
        }
    }

    // 글 삭제 - 게시글 삭제
    public function deleteWrite($writeModel, $writeId)
    {
       $write = Write::getWrite($this->board->id, $writeId);
       // 원글에 달린 댓글
       $comments = $writeModel->select('id')->where([
           'is_comment' => 1,
           'num' => $write->num
       ])->get()->toArray();
       // 댓글쓰기에 부여된 포인트 삭제
       foreach($comments as $comment) {
           // 포인트 삭제 및 사용 포인트 다시 부여
           $comment = Write::getWrite($this->board->id, $comment['id']);
           $deleteResult = deletePoint($comment->user_id, $this->board->table_name, $comment->id, '댓글');
           if($deleteResult == 0) {
               $insertResult = insertPoint($comment->user_id, $this->board->write_point * (-1), $this->board->subject. ' '. $comment->parent. '-'. $comment->id. ' 댓글삭제');
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
       deleteCache('main', $this->board->table_name);

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
