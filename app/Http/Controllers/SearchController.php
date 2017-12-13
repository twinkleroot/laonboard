<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Board;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use App\Models\Write;
use App\Models\CustomPaginator;

class SearchController extends Controller
{
    public $kind;
    public $keyword;
    public $operator;
    public $groupId;
    public $boardName;
    public $pageRow = 10;
    public $page;
    public $user;
    public $userLevel;

    public function result(Request $request)
    {
        $keyword = $request->filled('keyword') ? $request->keyword : '';   // 검색어
        $this->keyword = getSearchString($keyword);
        $this->kind = $request->filled('kind') ? $request->kind : 'subject||content';    // 검색필드
        $this->operator = $request->filled('operator') ? $request->operator : '';    // 연산자
        $this->groupId = $request->filled('groupId') ? $request->groupId : '';   // 그룹명
        $this->boardName = $request->filled('boardName') ? $request->boardName : '';    // 게시판 테이블 명
        $this->page = $request->filled('page') ? $request->page : 1 ;
        $this->user = auth()->user();
        $this->userLevel = $this->user ? $this->user->level : 1;

        $kinds = explode('||', trim($this->kind));                            // 검색필드를 구분자로 나눈다.
        $keywords = explode(' ', strip_tags($this->keyword));                 // 검색어를 구분자로 나눈다.

        // 검색 조건에 따라 Board 모델을 구한다.
        $boards = $this->getBoards();
        // 접근할 수 없는 그룹을 조회한 Board모델에서 제외 시킨다.
        $boards = $this->rejectGroupAccess($boards);
        // 게시판 마다 해당 검색필드와 검색어로 검색한다.
        $writes = $this->searchWrites($request, $boards, $keywords, $kinds);
        // 쿼리 스트링 만들기
        $queryStrings = $this->createQueryStrings();
        // 모델들을 합치고 page마다 10개 단위로 나눠서 CustomPaginator로 보내서 화면에 처리한다.
        $writesWithPagination = $this->mergeAndPaginate($writes, $queryStrings['pagination']);

        fireEvent('afterSearch');

        $params = [
            'groups' => Group::orderBy('group_id')->get(),
            'writes' => $writesWithPagination,
            'boards' => $writes,
            'kind' => $this->kind,
            'keyword' => $this->keyword,
            'operator' => $this->operator,
            'groupId' => $this->groupId,
            'boardName' => $this->boardName,
            'page' => $this->page,
            'commonQueryString' => $queryStrings['common'],
            'allBoardTabQueryString' => $queryStrings['allBoardTab'],
            'boardTabQueryString' => $queryStrings['boardTab'],
            'paginationQueryString' => $queryStrings['pagination']
        ];

        $theme = cache('config.theme')->name ? : 'default';
        $skin = cache('config.homepage')->searchSkin ? : 'default';

        return viewDefault("$theme.searches.$skin.result", $params);
    }

    // 검색 조건에 따라 Board 모델을 구한다.
    private function getBoards()
    {
        $boardQuery = Board::with('group')->where('use_search', 1)
            ->where('list_level', '<=', $this->userLevel);

        if($this->groupId) {
            $boardQuery = $boardQuery->where('group_id', $this->groupId);
        }
        if($this->boardName) {
            $boardQuery = $boardQuery->where('table_name', $this->boardName);
        }

        $boards = $boardQuery->orderBy('order', 'group_id', 'table_name')->get();

        return $boards;
    }

    // 접근할 수 없는 그룹을 조회한 Board모델에서 제외 시킨다.
    private function rejectGroupAccess($boards)
    {
        $user = $this->user;
        $groupUserList = [];
        foreach($boards as $key => $value) {
            if(!auth()->check() || !$user->isSuperAdmin()) {  // 비회원이거나 최고관리자가 아닐때
                $group = $value->group;

                if($group->use_access) {    // 그룹 접근을 사용할 때
                    if(!auth()->check()) {
                        $boards->pull($key);
                    } else if(($group->admin && !$user->isGroupAdmin($group))) { // 비회원이거나 그룹관리자가 존재하고 그룹관리자가 아닐떄
                        $userId = auth()->check() ? $user->id : 0;
                        if( !array_has($groupUserList, $group->id)) {
                            $groupUserList =
                                array_add($groupUserList, $group->id,
                                    GroupUser::
                                    where([
                                        'group_id' => $group->id,
                                        'user_id' => $userId,
                                    ])
                                    ->where('user_id', '<>', '')
                                    ->first()
                                );
                        }

                        if(!$groupUserList[$group->id]) {
                            $boards->pull($key);
                        }
                    }
                }
            }
        }

        return $boards;
    }

    // 게시판마다 해당 검색필드와 검색어로 검색해서 Write모델을 모은 컬렉션으로 리턴한다.
    private function searchWrites($request, $boards, $keywords, $kinds)
    {
        $result = [];
        // 게시판 마다 해당 검색필드와 검색어로 검색한다.
        $boardIndex = 0;
        if($this->keyword) {
            foreach($boards as $board) {
                $writeModel = new Write();
                $writeModel->board = $board;
                $writeModel->setTableName($board->table_name);
                $query = $writeModel
                    ->select($writeModel->getTable().'.*', 'users.id_hashKey as user_id_hashKey')
                    ->leftJoin('users', 'users.id', '=', $writeModel->getTable().'.user_id')
                    ->whereRaw('1=1');

                // 검색어 만큼 루프를 돌린다.
                foreach($keywords as $searchStr) {
                    if(trim($searchStr) == '') {
                        continue;
                    }

                    // 첫번째 검색필드 땐 operator에 따라 where 메소드 넣기, 나머진 orWhere()
                    for($i=0; $i<notNullCount($kinds); $i++) {
                        $op = ($this->operator == 'or') ? 'or' : 'and';
                        switch ($kinds[$i]) {
                            case 'email':
                            case 'name':
                                if($i == 0 && $op == 'and') {
                                    $query = $query->where($writeModel->getTable(). '.'. $kinds[$i], $searchStr);
                                } else {
                                    $query = $query->orWhere($writeModel->getTable(). '.'. $kinds[$i], $searchStr);
                                }
                                break;
                            case 'subject':
                            case 'content':
                                if (preg_match("/[a-zA-Z]/", $searchStr)) {
                                    $whereStr = "INSTR(LOWER($kinds[$i]), LOWER('$searchStr'))";
                                } else {
                                    $whereStr = "INSTR($kinds[$i], '$searchStr')";
                                }

                                if($i == 0 && $op == 'and') {
                                    $query = $query->whereRaw($whereStr);
                                } else {
                                    $query = $query->orWhereRaw($whereStr);
                                }
                                break;
                            default:
                                if($i == 0 && $op == 'and') {
                                    $query = $query->whereRaw("1=0");
                                } else {
                                    $query = $query->orWhereRaw("1=0");
                                }
                                break;
                        }
                    }
                }
                $writes = $query->get();
                // 검색 결과로 내보낼 게시물을 재가공 한다.
                $writes = $this->recreateWrites($request, $writes, $board, $writeModel, $kinds, $keywords);

                // 검색된 게시물이 있는 게시판만 결과물에 포함한다.
                if(notNullCount($writes) > 0) {
                    $result[$board->id] = $writes;
                }

                $boardIndex++;
            }
        }

        return $result;
    }

    // 검색 결과물 가공하기
    private function recreateWrites($request, $writes, $board, $writeModel, $kinds, $keywords)
    {
        $writes->boardSubject = $board->subject;
        $writes->boardId = $board->id;
        $writes->boardName = $board->table_name;
        // 댓글 때문에 원글을 계속 조회하는 문제 수정
        $tmpParent = 0;
        $parentWrite = null;
        // 각 게시물 row에 게시판 id를 넣어준다.
        foreach($writes as $write) {
            $subject = $write->subject;
            $content = $write->content;
            $queryString = "?kind=". $request->kind. "&keyword=". $request->keyword;
            // 댓글일 경우 부모글의 제목을 댓글의 제목으로 넣기
            if($write->is_comment) {
                $queryString .= '#comment'. $write->id;
                if($tmpParent != $write->parent) {
                    $parentWrite = $writeModel->where('id', $write->parent)->first();
                    $tmpParent = $parentWrite->id;
                }

                $subject = '댓글 | '. $parentWrite->subject;
            } else {
                $parentWrite = $write;
            }
            $subject = convertText($subject);
            // 검색어 색깔 다르게 표시
            if( in_array('subject', $kinds) ) {
                $subject = searchKeyword($keywords, $subject);
            }

            // 댓글이나 원글 중 비밀글이 포함되어 있을 경우 표시
            if( strstr($write->option. $parentWrite->option, 'secret')) {
                $content = '[비밀글 입니다.]';
            } else {
                if($board->read_level <= $this->userLevel) {
                    $content = strip_tags($content);
                    $content = convertText($content, 1);
                    $content = strip_tags($content);
                    $content = str_replace('&nbsp;', '', $content);
                    $content = cutString($content, 300, "…");
                }

                if( in_array('content', $kinds) ) {
                    $content = searchKeyword($keywords, $content);
                }
            }

            $write->subject = $subject;
            $write->content = $content;
            $write->boardSubject = $board->subject;
            $write->boardName = $board->table_name;
            $write->queryString = $queryString;
        }

        return $writes;
    }

    private function createQueryStrings()
    {
        // 모두 필요한 파라미터
        $commonArray = [
            'kind' => $this->kind,
            'keyword' => $this->keyword,
            'operator' => $this->operator,
        ];
        $commonQueryString = $this->assemblyQueryString($commonArray);

        // 전체 게시판 탭
        $allBoardTabArray = $this->groupId ? array_add($commonArray, 'groupId', $this->groupId) : $commonArray;
        $allBoardTabQueryString = $this->assemblyQueryString($allBoardTabArray);

        // 상단 게시판 탭
        $boardTabArray = array_collapse([$commonArray, [
            'groupId' => $this->groupId,
            'boardName' => $this->boardName,
        ]]);
        $boardTabQueryString = $this->assemblyQueryString($boardTabArray);

        // 페이징 링크
        $paginationArray = ($this->page > 1) ? array_add($boardTabArray, 'page', $this->page) : $boardTabArray;
        $paginationQueryString = $this->assemblyQueryString($paginationArray);

        return [
            'common' => $commonQueryString,
            'allBoardTab' => $allBoardTabQueryString,
            'boardTab' => $boardTabQueryString,
            'pagination' => $paginationQueryString,
        ];
    }

    // 쿼리 스트링 조립
    private function assemblyQueryString($querys)
    {
        $result = [];
        foreach($querys as $key => $value) {
            if($value) {
                $result[] = "$key=$value";
            }
        }
        return implode('&', $result);
    }

    // 결과 게시물 모델을 합치고 페이징한다.
    private function mergeAndPaginate($writes, $paginationQueryString)
    {
        $mergeWrites = collect();
        foreach($writes as $write) {
            $write[0]->boardChange = 1;
            $mergeWrites = $mergeWrites->merge($write);
        }
        $sliceWrites = $mergeWrites->slice($this->pageRow * ($this->page - 1), $this->pageRow);

        $writes = new CustomPaginator($sliceWrites, notNullCount($mergeWrites), $this->pageRow, $this->page);
        $writes->withPath('/search?'. $paginationQueryString);

        return $writes;
    }
}
