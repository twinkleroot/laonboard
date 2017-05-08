<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;
use DB;
use Carbon\Carbon;
use Exception;
use App\User;
use App\Board;
use App\Point;
use App\Common\Util;
use App\Common\StrEncrypt;
use App\Common\CustomPaginator;
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

    public function __construct($boardId, $attributes = [])
    {
        $this->board = Board::find($boardId);

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

        $kind = '';
        if($request->has('kind')) {
            $kind = $request->kind;
            $viewParams['kind'] = 'kind='. $kind;
        }

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
        $query = $writeModel
                ->selectRaw($writeModel->table.'.*, users.level as user_level')
                ->leftJoin('users', 'users.id', '=', $writeModel->table.'.user_id');

        // 어떤 필드를 기준으로 정렬할 것인지
        $sortField = is_null($this->board->sort_field) ? 'num, reply' : $this->board->sort_field;
        // 문자열 암복호화 클래스 생성
        // $strEncrypt = new StrEncrypt();

        if($currenctCategory != '') {
            $query = $query->where('ca_name', $currenctCategory);
        }

        // 검색 - 공지를 표시해주지 않는다.
        if($kind != '' && $keyword != '') {
            if($kind == 'user_id') {
                // 암호화된 user_id를 복호화해서 검색한다.
                $userId = decrypt($keyword);    // 라라벨 기본 지원 decrypt
                // $userId = $strEncrypt->decrypt($keyword);

                // 검색 쿼리 붙여서 공지를 가장 먼저 보여주는 페이징
                // $writes = $this->customPaging($request, $query->where($kind, $userId), $sortField);
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
                        'message' => $keyword . ' 사용자가 존재하지 않습니다.'
                    ];
                }
            // 단독 키워드 검색(제목, 내용)
            } else {
                $query = $query->where($kind, 'like', '%'.$keyword.'%');
            }

            // if($kind != 'user_id') {
                $writes = $query->orderByRaw($sortField)->paginate($this->board->page_rows);
            // }
        // 분류 선택
        } else if($currenctCategory != '') {
            $writes = $query->orderByRaw($sortField)->paginate($this->board->page_rows);
        } else {
            // 공지를 가장 먼저 보여주는 수동 페이징
            $writes = $this->customPaging($request, $query, $sortField);
        }


        // 뷰에 내보내는 아이디 검색의 링크url에는 암호화된 id를 링크로 건다.
        foreach($writes as $write) {
            // $write->user_id = $strEncrypt->encrypt($write->user_id);
            $write->user_id = encrypt($write->user_id);     // 라라벨 기본 지원 encrypt
        }

        return [
            'writes' => $writes,
            'message' => '',
        ];
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
        $writes->setPath($request->url());

        return $writes;
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

        $html = 0;
        if (strstr($write->option, 'html1')) {
            $html = 1;
        } else if (strstr($write->option, 'html2')) {
            $html = 2;
        }

        $write->content = Util::convertContent($write->content, $html);

        // 관리자 여부에 따라 ip 다르게 보여주기
        if( !session()->get('admin')) {
            if ( !is_null($write->ip)) {
                $write->ip = preg_replace("/([0-9]+).([0-9]+).([0-9]+).([0-9]+)/", config('gnu.IP_DISPLAY'), $write->ip);
            }
        }

        return [
            'board' => $this->board,
            'view' => $write,
            'request' => $request,
        ];
    }

    // 글 읽기 전 프로세스
    public function beforeRead($write, $request)
    {
        $sessionName = 'session_view_'. $this->board->table_name. '_'. $write->id;
        $hit = $write->hit;
        if(!session()->get($sessionName)) {
            // 조회수 증가
            $hit = $this->increaseHit($write);
            // 포인트 계산(차감)
            $message = $this->calculatePoint($write, $request);

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

    // 포인트 계산(차감)
    public function calculatePoint($write, $request)
    {
        $user = auth()->user();
        // 작성자가 본인이면 통과
        if($write->user_id > 0 && $write->user_id == $user->id) {
            ;
        } else if(is_null($user) && $this->board->read_level == 1 && $write->ip == $request->ip()) {
            ;
        } else {
            // 글읽기 포인트가 설정되어 있다면
            if (Config::getConfig('config.homepage')->usePoint
                && $this->board->read_point
                && $user->point + $this->board->read_point < 0) {
                    return '보유하신 포인트('.number_format($user->point).')가 없거나 모자라서 글읽기('.number_format($this->board->read_point).')가 불가합니다.\\n\\n포인트를 모으신 후 다시 글읽기 해 주십시오.';
            }

            // 포인트 계산하기
            // 포인트 부여(글쓰기, 댓글)
            Point::addPoint([
                'user' => $user,
                'relTable' => $this->board->table_name,
                'relEmail' => $write->id,
                'relAction' => '읽기',
                'content' => $this->board->subject . ' ' . $write->id . ' 글읽기',
                'type' => $this->board->read_point,
            ]);
        }

        return '';
    }

    // (게시판) 글 쓰기 페이지에서 필요한 파라미터 가져오기
    public function getBbsCreateParams($writeModel)
    {
        // $userLevel = is_null(Auth::user()) ? 1 : Auth::user()->level;
        $board = $this->board;
        $categories = [];
        if( !is_null($board->category_list) ) {
            $categories = explode('|', $board->category_list);
        }

        return [
            'board' => $board,
            'categories' => $categories,
            // 'userLevel' => $userLevel,
        ];
    }

    // 글쓰기 간격 검사
    public function checkWriteInterval()
    {
        $dt = Carbon::now();
        $interval = Config::getConfig('config.board')->delaySecond;

        if(!is_null(session()->get('postTime'))) {
            if(session()->get('postTime') >= $dt->subSecond($interval) && !session()->get('admin')) {
                return false;
            }
        }

        session()->put('postTime', Carbon::now());

        return true;
    }

    // 올바르지 않은 코드가 글 내용에 다수 들어가 있는지 검사
    public function checkIncorrectContent($request)
    {
        if (substr_count($request->content, '&#') > 50) {
            return false;
        }
        return true;
    }

    // 서버에서 지정한 Post의 최대 크기 검사
    public function checkPostMaxSize($request)
    {
        if (empty($_POST)) {
            return false;
        }
        return true;
    }

    // 관리자가 아닌데 공지사항을 남기려 하는 경우가 있는지 검사
    public function checkAdminAboutNotice($request)
    {
        if ( !session()->get('admin') && $request->has('notice') ) {
    		return false;
        }
        return true;
    }

    // (게시판) 글 쓰기 -> 저장
    public function storeWrite($writeModel, $request)
    {
        $inputData = $request->all();
        $inputData = array_except($inputData, ['_token', 'file_content', 'attach_file', 'html', 'secret', 'mail', 'notice']);    // csrf 토큰 값 제외

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

        // 회원 글쓰기 일 때
        if( !is_null($user) ) {
            // 실명을 사용할 때
            if($this->board->use_name) {
                $name = $user->name;
            } else {
                $name = $user->nick;
            }

            $userId = $user->id;
            $password = $user->password;
        }

        $insertData = array_collapse([
            $inputData,
            [
                'user_id' => $userId,
                'name' => is_null($user) ? $inputData['name'] : $name,
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
        Point::addPoint([
            'user' => $user,
            'relTable' => $this->board->table_name,
            'relEmail' => $lastInsertId,
            'relAction' => $relAction,
            'content' => $content,
            'type' => $pointType,
        ]);

        // 공지사항인 경우 boards에 등록하기
        if($request->has('notice')) {
            $insertData = array_except($insertData, 'notice');    // notice 값은 writes가 아닌  boards에 저장
            $notice = '';
            if( !is_null($this->board->notice) ) {
                $notice = $this->board->notice . ',' . $lastInsertId;
            } else {
                $notice = $lastInsertId;
            }

            $this->board->notice = $notice;
            $this->board->save();
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

        return $lastInsertId;
    }

    // (게시판) 글 선택 삭제
    public function selectDeleteWrites($writeModel, $ids)
    {
        // 답변 글이 있는 경우 처리 추가 필요

        // 첨부파일도 함께 삭제한다.
        $idsArr = explode(',', $ids);
        foreach($idsArr as $id) {
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

    // (게시판) 게시물 복사, 게시물 이동 = 복사 + 기존 테이블에서 삭제
    // $writeModel : 원본 게시물 데이터 모델
    // $writeIds : 복사할 대상 게시물들의 id
    // $boardIds : 선택한 대상 게시판들의 id
    public function copyWrites($writeModel, $request)
    {
        $writeIds = session()->get('writeIds');
        $boardIds = $request->chk_id;

        // 복사할 대상 게시물들
        $originalWrites = $writeModel->whereIn('id', $writeIds)->get()->toArray();
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
                $message = $this->deleteWrites($writeModel);
            }

        } else {
            $message = '게시물 복사에 실패하였습니다.';
        }

        return $message;
     }

     // 게시물 복사할 때 첨부파일정보도 함께 복사하는 메서드
     public function updateAttachedFileInfo($writeModel, $write, $lastInsertId, $board, $request)
     {
         $boardFiles = BoardFile::where([
             'board_id' => $this->board->id,
             'write_id' => $write['id']
         ])->get();

         foreach($boardFiles as $boardFile) {
             $copyBoardFile = $boardFile->attributes;
             $copyBoardFile['write_id'] = $lastInsertId;
             $copyBoardFile['board_id'] = $board->id;

             BoardFile::insert($copyBoardFile);
             // 모델의 primary key를 지정하기가 어려워서 update 대신 insert하고 delete하는 방식을 택함.
             if($request->type == 'move') {
                 BoardFile::where([
                     'board_id' => $this->board->id,
                     'write_id' => $write['id'],
                     'board_file_no' => $boardFile->board_file_no
                 ])->delete();
             }
         }
     }

     // 게시물 이동할 때 기존 원본 게시물만 삭제 (첨부파일 정보변경은 updateAttachedFileInfo() 에서 한다.)
     public function deleteWrites($writeModel)
     {
         $writeIds = session()->get('writeIds'); // 복사할 대상 게시물들의 id

         $result = $writeModel->whereRaw('id in (' . implode(",", $writeIds) . ') ')->delete();

        if($result > 0) {
            return '게시물 이동에 성공하였습니다.';
        } else {
            return '게시물 이동이 실패하였습니다.';
        }
     }

}
