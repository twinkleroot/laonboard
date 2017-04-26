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
use App\Common\Util;
use App\Common\StrEncrypt;


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

    // (게시판) index 페이지에서 필요한 파라미터 가져오기
    public function getBbsIndexParams($writeModel, $kind='', $keyword='')
    {
        $userLevel = is_null(Auth::user()) ? 1 : Auth::user()->level;
        $writes;
        try {
            $writes = $this->getWrites($writeModel, $kind, $keyword);
        } catch (Exception $e) {
            return [
                'message' => '존재하지 않는 게시판입니다.',
                'redirect' => '/'
            ];
        }

        return [
            'board' => $this->board,
            'writes' => $writes,
            'userLevel' => $userLevel,
            'kind' => $kind,
            'keyword' => $keyword,
        ];
    }

    // (게시판 리스트) 해당 커뮤니티 게시판 모델을 가져온다. (검색 포함)
    public function getWrites($writeModel, $kind, $keyword)
    {
        $query = $writeModel;

        // 문자열 암복호화 클래스 생성
        $strEncrypt = new StrEncrypt();

        if($kind != '' && $keyword != '') {
            if($kind == 'user_id') {
                //암호화된 user_id를 복호화해서 검색한다.
                $userId = $strEncrypt->decrypt($keyword);
                // 검색 쿼리 붙이기
                $query = $query->where($kind, $userId);
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
                try {
                    $query = $query->where('user_id', $user->id)
                    ->where('is_comment', $kinds[1]);
                } catch (Exception $e) {
                    return view('message', ['message' => $keyword . ' 사용자가 존재하지 않습니다.']);
                }
            // 단독 키워드 검색(제목, 내용)
            } else {
                $query = $query->where($kind, 'like', '%'.$keyword.'%');
            }
        }
        // 어떤 필드를 기준으로 정렬할 것인지
        $sortField = is_null($this->board->sort_field) ? 'num, reply' : $this->board->sort_field;

        // 뷰에 내보내는 아이디 검색의 링크url에는 암호화된 id를 링크로 건다.
        $writes = $query->orderByRaw($sortField)->paginate($this->board->page_rows);
        foreach($writes as $write) {
            $write->user_id = $strEncrypt->encrypt($write->user_id);
        }

        return $writes;
    }

    // (게시판) 글 쓰기 페이지에서 필요한 파라미터 가져오기
    public function getBbsCreateParams($writeModel)
    {
        // $userLevel = is_null(Auth::user()) ? 1 : Auth::user()->level;

        return [
            'board' => $writeModel->board,
            // 'userLevel' => $userLevel,
        ];
    }

    // (게시판) 글 쓰기 -> 저장
    public function storeWrite($writeModel, $request)
    {

        $inputData = $request->all();
        $inputData = array_except($inputData, '_token');    // csrf 토큰 값 제외

        $user = Auth::user();
        $userId = 1;    // $userId가 1이면 비회원
        $name = '';
        $password = '';
        $minNum = $writeModel->min('num');

        // 회원 글쓰기 일 때
        if( !is_null($user) ) {
            // 실명을 사용할 때
            if($writeModel->board->use_name) {
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
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
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
        }

        // 댓글 or 답변글일 경우 원글의 num을 가져와서 넣는다.
        // if( (isset($inputData['is_comment']) && $inputData['is_comment'] == 1)
        //     || (isset($inputData['reply']) && $inputData['reply'] != '') ) {
        //     ;
        // }

        // 답변글일 경우 reply에 댓글 레벨을 표시한다. (inputData에는 원글의 reply 받아오기)
        // reply 구하는 공식 적용

        // 댓글일 경우 원글의 last에 최근 댓글 달린 시간을 업데이트한다.

        return $writeModel->where('id', $lastInsertId)->update($toUpdateColumn);
    }

    // (게시판) 글 선택 삭제
    public function selectDeleteWrites($writeModel, $ids)
    {
        // 답변 글이 있는 경우 처리 추가 필요
        $result = $writeModel->whereRaw('id in (' . $ids . ') ')->delete();

        if($result > 0) {
            return '선택한 글이 삭제되었습니다.';
        } else {
            return '선택한 글의 삭제가 실패하였습니다.';
        }
    }

    // (게시판) 게시물 복사 및 이동
    public function copyWrites($writeModel, $request)
    {
        $writeIds = session()->get('writeIds'); // 복사할 대상 게시물들의 id
        $boardIds = $request->chk_id;   // 복사되는 대상 게시판들의 id

        $originalWrites = $writeModel->whereIn('id', $writeIds)->get()->toArray();

        $index = 0;
        foreach($originalWrites as $write) {
            // 원래 테이블에서의 id 제거
            $originalWrites[$index++] = array_except($write, 'id');
        }

        $boards = Board::whereIn('id', $boardIds)->get();

        if( !is_null($boards)) {
            foreach($boards as $board) {
                // 게시판 테이블 셋팅
                // $destinationWrite : 복사되는 게시판
                $destinationWrite = new Write($board->table_name);
                $destinationWrite->setTableName($board->table_name);
                // num의 최소값
                $minNum = is_null($destinationWrite->min('num')) ? 0 : $destinationWrite->min('num');

                // $originalWrites : 복사할 글들
                // 댓글도 함께 복사 처리가 추가 되야 함
                foreach($originalWrites as $write) {
                    // 복사할 글을 복사한 테이블에 맞춰서 num 재설정
                    // $write['num'] = --$minNum;
                    $destinationWrite->insert($write);

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
                }
            }
        } else {
            return '게시물 복사에 실패하였습니다.';
        }

        return '게시물 복사가 완료되었습니다.';
     }

     // 게시물 이동 (옮긴 게시물 삭제)
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
