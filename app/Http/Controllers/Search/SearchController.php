<?php

namespace App\Http\Controllers\Search;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    public function result(Request $request)
    {
        // 1
        // 검색어를 받아서 특수문자를 제거한다.
        // or, and를 받는다.
        // 그룹 id를 받는다. 아니면 전체
        // 검색필드를 찾는다. (제목+내용, 제목, 내용, 회원이메일, 회원이름)
        // 한 게시판마다 3개의 게시물만 나오도록 한다.
        // boards 테이블에서의 검색 : group_id, table_name, read_level 을 검색
        // 검색 조건1 : use_search = 1, list_level <= $user->list_level
        // 검색 조건2 : 그룹을 선택했으면 group_id = $groupId
        // 검색 조건3 : 게시판을 선택했으면 table_name = $tableName
        // 정렬 : order by order, group_id, table_name

        // 2
        // 이렇게 나온 $boards를 foreach돌리면서 해당 게시판이 속한 그룹의 use_access, admin(그룹관리자)를 검색한다.
        // 그룹 접근을 사용할 때
        // 현재 그룹에 현재 $user가 접근할 수 있는지 group_users를 검색한다.(그룹 관리자가 있고 $user가 그룹 관리자면 통과)
        // 그렇게 해서 접근할 수 있는 테이블명을 최종 검색할 table_name과 read_level로 배열에 저장한다.

        // 3 - where절 만들기
        // stripslashes() -> getText() 로 검색 결과 input창에 표시할 검색어를 정제한다. 모던 php에서도 할만한지 찾아보기.
        // 특수문자를 제거한 검색어를 구분자로 나눈다.
        // 검색어의 공백을 제거한다.
        // 검색필드를 || 구분자로 나눈다.
        // 검색필드 수 만큼 다중 필드 검색이 가능하게 한다. query()에 where()절을 더할 수 있도록 한다.
        // 내용 검색일 때 영어로 된 검색어와 아닌 검색어를 구분해서 처리한다.
        // switch_case 문에 없는 검색필드는 항상 거짓 결과로 나오도록 처리한다.

        // 4
        // 3의 where절로 검색할 테이블마다 검색해서 각 게시판마다 모델객체를 내보낸다.
        // 모델들을 합치고 page마다 10개 단위로 나눠서 CustomPaginator로 보내서 화면에 처리한다.
        return view('search.default.result');
    }
}
