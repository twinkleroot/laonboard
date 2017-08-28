@section('fisrt_include_css')
    <link rel="alternate" type="application/rss+xml" href="{{ url('rss') }}" title="RSS Feed {{ config('rss.title') }}">
@endsection

<!-- Board list start -->
<form name="fBoardList" id="fBoardList" action="" onsubmit="return formBoardListSubmit(this);" method="post" target="move">
    <input type="hidden" id='_method' name='_method' value='post' />
    <input type="hidden" id='type' name='type' value='' />
    <input type="hidden" id='page' name='page' value='{{ $writes->currentPage() }}' />
    {{ csrf_field() }}

    <div class="pull-left bd_head">
        <a href="{{ route('board.index', $board->table_name) }}">{{ $board->subject }}</a><span>전체 {{ $writes->total() }}건 {{ $writes->currentPage() }}페이지</span>
    </div>

    <div class="bd_btn">
        <ul id="bd_btn" class="pull-right">
            @if(auth()->user() && auth()->user()->isBoardAdmin($board))
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle bd_rd_more" data-toggle="dropdown" role="button" aria-expanded="false">
                        <button type="" class="btn btn-danger">
                            <i class="fa fa-cog"></i> 관리
                        </button>
                    </a>
                    <ul class="dropdown-menu bd_adm" role="menu">
                        <li><input type="submit" value="선택삭제" onclick="document.pressed=this.value"/></li>
                        <li><input type="submit" value="선택복사" onclick="document.pressed=this.value"/></li>
                        <li><input type="submit" value="선택이동" onclick="document.pressed=this.value"/></li>
                        <li><a href="{{ route('admin.boards.edit', $board->table_name) }}">게시판 설정</a></li>
                    </ul>
                </li>
            @endif

            <li class="mr0">
                @if($board->use_rss_view && $board->list_level == 1 && $board->read_level == 1)
                    <button type="button" class="btn btn-sir" onclick="location.href='{{ route('rss', $board->table_name) }}'">
                        RSS
                    </button>
                @endif
                <button type="button" class="btn btn-sir" onclick="location.href='{{ route('board.create', $board->table_name). '?'. $request->getQueryString() }}'">
                    <i class="fa fa-pencil"></i> 글쓰기
                </button>
            </li>
        </ul>
    </div>

    @if($board->use_category == 1 )
    <div class="bd_category">
        <ul>
            <!-- 선택된 카테고리의 class에 on 추가 -->
            <li class="btn" id="all"><a href="{{ route('board.index', $board->table_name) }}">전체</a></li>
            @foreach($categories as $category)
                <li class="btn" id="{{ $category }}"><a href="{{ route('board.index', $board->table_name). '?category='. $category }}">{{ $category }}</a></li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- 리스트형 게시판 -->
    <table class="table box">
        <thead>
            <tr>
                <th @if(count($writes) == 0)class="bd_num"@endif>번호</th>
                @if(auth()->user() && auth()->user()->isBoardAdmin($board))
                    <th @if(count($writes) == 0)class="bd_check"@endif> <!-- 전체선택 -->
                        <input type="checkbox" name="chkAll" onclick="checkAll(this.form)">
                    </th>
                @endif
                <th>제목</th>
                <th @if(count($writes) == 0)class="bd_name"@endif>글쓴이</th>
                <th @if(count($writes) == 0)class="bd_date"@endif>날짜</th>
                <th @if(count($writes) == 0)class="bd_hits"@endif>조회</th>
                @if($board->use_good)
                <th @if(count($writes) == 0)class="bd_re"@endif>추천</th>
                @endif
                @if($board->use_nogood)
                <th @if(count($writes) == 0)class="bd_nre"@endif>비추천</th>
                @endif
            </tr>
        </thead>
        <tbody>
        @if(count($writes) > 0)
        @foreach($writes as $write)
            @if($kind != 'user_id' && in_array($write->id, $notices) && $search == 0 && $currenctCategory == '')
            <tr id="bd_notice">
            @else
            <tr>
            @endif
                <!-- 공지사항 기능 넣으면 공지사항 까지 포함시켜서 넘버링 -->
                <td class="bd_num">
                    @if($kind != 'user_id' && in_array($write->id, $notices) && $search == 0 && $currenctCategory == '')
                        공지
                    @elseif(isset($request->writeId) && $request->writeId == $write->id)
                        <span class="read">열람중</span>
                    @else
                        {{ $writes->total() - ($writes->currentPage() - 1) * $board->page_rows - $loop->index }}
                    @endif
                </td>
                @if(auth()->user() && auth()->user()->isBoardAdmin($board))
                    <td class="bd_check"><input type="checkbox" name="chkId[]" class="writeId" value='{{ $write->id }}'></td>
                @endif
                <td @if($write->reply != '') class="bd_reply" style="padding-left: calc(20px * {{ strlen($write->reply) }} @endif">

                    <span class="bd_subject">
                        @if($board->use_category)
                        <a href="{{ route('board.index', $board->table_name). '?category='. $write->ca_name }}" class="subject_cg">{{ $write->ca_name }}</a>
                        @endif
                        <a href="/bbs/{{ $board->table_name }}/view/{{ $write->parent }}?{{ Request::getQueryString() }}">
                            {{ $write->subject }}
                        </a>
                        {{-- 글올린시간 + 설정에 있는 신규 글 시간 > 현재 시간 --}}
                        @if(date($write->created_at->addHours(24)) > date("Y-m-d H:i:s", time()) && $board->new != 0 )
                        <img src="/themes/default/images/icon_new.gif"> <!-- 새글 -->
                        @endif
                        @if($write->file > 0)
                        <img src="/themes/default/images/icon_file.gif"> <!-- 파일 -->
                        @endif
                        @if($write->link1 || $write->link2)
                        <img src="/themes/default/images/icon_link.gif"> <!-- 링크 -->
                        @endif
                        @if($write->hit >= $board->hot)
                        <img src="/themes/default/images/icon_hot.gif"> <!-- 인기 -->
                        @endif
                        @if(str_contains($write->option, 'secret'))
                        <img src="/themes/default/images/icon_secret.gif"> <!-- 비밀 -->
                        @endif
                        @if($write->comment > 0)
                        <span class="bd_cmt">{{ $write->comment }}</span>
                        @endif
                    </span>
                </td>
                <td class="bd_name">
                @if(auth()->user() && $board->use_sideview)
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">{{ $write->name }}</a>
                    <ul class="dropdown-menu" role="menu">
                    @if($write->user_level)
                        @component('board.sideview', ['board' => $board, 'id' => $write->user_id, 'name' => $write->name, 'email' => $write->email, 'category' => $currenctCategory])
                        @endcomponent
                    @else
                        <li><a href="/bbs/{{ $board->table_name }}?kind=name&amp;keyword={{ $write->name }}&amp;category={{ $currenctCategory }}">이름으로 검색</a></li>
                    @endif
                    @if($write->user_level)
                        <li><a href="{{ route('new.index') }}?nick={{ $write->name }}">전체게시물</a></li>
                    @endif
                    </ul>
                @elseif(auth()->guest() && $board->use_sideview)
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">{{ $write->name }}</a>
                    <ul class="dropdown-menu" role="menu">
                    @if($write->user_level)
                        @component('board.sideview', ['board' => $board, 'id' => $write->user_id, 'name' => $write->name, 'email' => $write->email, 'category' => $currenctCategory])
                        @endcomponent
                    @else
                        <li><a href="/bbs/{{ $board->table_name }}?kind=name&amp;keyword={{ $write->name }}&amp;category={{ $currenctCategory }}">이름으로 검색</a></li>
                    @endif
                    @if($write->user_level)
                        <li><a href="{{ route('new.index') }}?nick={{ $write->name }}">전체게시물</a></li>
                    @endif
                    </ul>
                @else
                    {{ $write->name }}
                @endif
                </td>
                <td class="bd_date">@monthAndDay($write->created_at)</td>
                <td class="bd_hits">{{ $write->hit }}</td>
                @if($board->use_good)
                <td class="bd_re"><span class="up">{{ $write->good }}</span></td>
                @endif
                @if($board->use_nogood)
                <td class="bd_nre">{{ $write->nogood }}</td>
                @endif
            </tr>
        @endforeach
        @else
            @php
                $colspan = 6;
                if($board->use_good) {
                    $colspan++;
                }
                if($board->use_nogood) {
                    $colspan++;
                }
            @endphp
            <tr>
                <td colspan="{{ $colspan }}">
                    <span class="empty_table">
                        <i class="fa fa-exclamation-triangle"></i> 게시물이 없습니다.
                    </span>
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</form>

<div class="bd_btn">
    <ul class="pull-left">
        <li id="pt_sch">
            <form method="get" action="{{ route('board.index', $board->table_name) }}" onsubmit="return searchFormSubmit(this);">
                @if($currenctCategory != '')
                    <input type="hidden" id='category' name='category' value='{{ $currenctCategory }}' />
                @endif
                <label for="kind" class="sr-only">검색대상</label>
                <select name="kind" id="kind">
                    <option value="subject" @if($kind == 'subject') selected @endif>제목</option>
                    <option value="content" @if($kind == 'content') selected @endif>내용</option>
                    <option value="subject || content" @if($kind == 'subject || content') selected @endif>제목+내용</option>
                    <option value="name, 0" @if($kind == 'name, 0') selected @endif>글쓴이</option>
                    <option value="name, 1" @if($kind == 'name, 1') selected @endif>글쓴이(코멘트 포함)</option>
                </select>

                <label for="keyword" class="sr-only">검색어</label>
                <input type="text" name="keyword" id="keyword" value="{{ $kind != 'user_id' ? $keyword : '' }}" class="search" required>
                <button type="submit" class="search-icon">
                    <i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">검색</span>
                </button>
            </form>
        </li>
    </ul>

    <ul id="bd_btn">
        <li class="mr0">
            <button type="button" class="btn btn-sir" onclick="location.href='{{ route('board.create', $board->table_name). '?'. $request->getQueryString() }}'">
                <i class="fa fa-pencil"></i> 글쓰기
            </button>
        </li>
    </ul>
</div>

{{-- 페이지 처리 --}}
{{ $writes->appends(Request::except('page'))->withPath('/bbs/'. $board->table_name)->links() }}

@if($board->use_category == 1 )
<script>
$(function(){
    var category = "{{ $currenctCategory }}";
    if(category != "") {
        // document.getElementById(category).addClass
        // $("div[id='" + category + "']'").addClass('on');
        document.getElementById(category).className += ' on'
    } else {
        document.getElementById('all').className += ' on'
    }
});
</script>
@endif
<script>
function searchFormSubmit(f) {
    if(f.keyword.value.trim() == '') {
        alert('검색어 : 필수 입력입니다.');
        return false;
    }

    return true;
}

// 관리자 메뉴 폼 서브밋 전 실행되는 함수
function formBoardListSubmit(f) {
    var selected_id_array = selectIdsByCheckBox(".writeId");

    if(selected_id_array.length == 0) {
        alert(document.pressed + '할 게시물을 한 개 이상 선택하세요.')
        return false;
    }

    if(document.pressed == "선택복사") {
        selectCopy("copy");
        return;
    }

    if(document.pressed == "선택이동") {
        selectCopy("move");
        return;
    }

    if(document.pressed == "선택삭제") {
        if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다\n\n답변글이 있는 게시글을 선택하신 경우\n답변글도 선택하셔야 게시글이 삭제됩니다.")) {
            return false;
        }

        f.removeAttribute("target");
        f.action = '/bbs/{{ $board->table_name }}/delete/ids/' + selected_id_array;
        f._method.value = 'DELETE';
    }

    return true;
}

// 선택한 게시물 복사 및 이동
function selectCopy(type) {
    var f = document.fBoardList;

    if (type == "copy") {
        str = "복사";
    } else {
        str = "이동";
    }

    var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");

    f.type.value = type;
    f.target = "move";
    f.action = "{{ route('board.list.move', $board->table_name)}}";
    f.submit();
}
</script>
