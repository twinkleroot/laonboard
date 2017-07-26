@section('fisrt_include_css')
<link rel="alternate" type="application/rss+xml" href="{{ url('rss') }}" title="RSS Feed {{ config('rss.title') }}">
@endsection

<!-- Board start -->
<form name="fBoardList" id="fBoardList" action="" onsubmit="return formBoardListSubmit(this);" method="post" target="move">
    <input type="hidden" id='_method' name='_method' value='post' />
    <input type="hidden" id='type' name='type' value='' />
    <input type="hidden" id='page' name='page' value='{{ $writes->currentPage() }}' />
    {{ csrf_field() }}

    <div class="pull-left bd_head">
        <span><a href="{{ route('board.index', $board->id) }}">{{ $board->subject }}</a> 전체 {{ $writes->total() }}건 {{ $writes->currentPage() }}페이지</span>
    </div>

    <div class="bd_btn">
        <ul id="bd_btn" class="pull-right">
            @if(session()->get('admin'))
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
                    <li><a href="{{ route('admin.boards.edit', $board->id) }}">게시판 설정</a></li>
                </ul>
            </li>
            @endif

            <li class="mr0">
                @if($board->use_rss_view && $board->list_level == 1 && $board->read_level == 1)
                <button type="button" class="btn btn-sir" onclick="location.href='{{ route('rss', $board->id) }}'">
                    RSS
                </button>
                @endif
                <button type="button" class="btn btn-sir" onclick="location.href='{{ route('board.create', $board->id). '?'. $request->getQueryString() }}'">
                    <i class="fa fa-pencil"></i> 글쓰기
                </button>
            </li>
        </ul>
    </div>

    @if($board->use_category == 1 )
    <div class="bd_category">
        <ul>
            <!-- 선택된 카테고리의 class에 on 추가 -->
            <li class="btn" id="all"><a href="{{ route('board.index', $board->id) }}">전체</a></li>
            @foreach($categories as $category)
            <li class="btn" id="{{ $category }}"><a href="{{ route('board.index', $board->id). '?category='. $category }}">{{ $category }}</a></li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- 갤러리형 게시판 -->
    <input type="checkbox" name="chkAll" onclick="checkAll(this.form)"> <!-- 전체선택 -->
    <div id="gry" class="row">
        @if(count($writes) > 0)
        @foreach($writes as $write)
        <div class="col-md-3 col-sm-6 col-xs-12 gry"> <!-- 한줄에 4개 배치 -->
            <input type="checkbox" name="chkId[]" class="writeId" value='{{ $write->id }}'>
            <div>
                <div class="gry_img" style="height:{{ $board->gallery_height }}px;"> <!-- height 기본값 150px로 css처리 해둠 -->
                    @if($viewParams == '')
                    <a href="/board/{{ $board->id }}/view/{{ $write->parent }}">
                    @else
                    <a href="/board/{{ $board->id }}/view/{{ $write->parent }}?{{ $viewParams }}">
                    @endif
                        @if($write->listThumbnailPath == '공지' || $write->listThumbnailPath == 'no image')
                            <span class="gry_txt" style="padding: calc( {{ $board->gallery_height }}px / 2 - 10px ) 0;">{{ $write->listThumbnailPath }}</span>
                        @else
                        <img src="{{ $write->listThumbnailPath }}" style="width:100%;min-height:{{ $board->gallery_height }}px;">
                        @endif
                    </a>
                </div>
                <div class="gry_info">
                    <p>
                        <span class="bd_subject">
                            @if($board->use_category == 1 )
                            <a href="{{ route('board.index', $board->id). '?category='. $write->ca_name }}" class="subject_cg">{{ $write->ca_name }}</a>
                            @endif
                            @if($viewParams == '')
                            <a href="/board/{{ $board->id }}/view/{{ $write->parent }}">
                                @if(isset($request->writeId) && $request->writeId == $write->id)
                                <span class="read">    {{-- 열람중 --}}
                                    {!! $write->subject !!}
                                </span>
                                @else
                                {!! $write->subject !!}
                                @endif
                            </a>
                            @else
                            <a href="/board/{{ $board->id }}/view/{{ $write->parent }}?{{ $viewParams }}">
                                @if(isset($request->writeId) && $request->writeId == $write->id)
                                <span class="read">    {{-- 열람중 --}}
                                    {!! $write->subject !!}
                                </span>
                                @else
                                {!! $write->subject !!}
                                @endif
                            @endif
                            {{-- 글올린시간 + 설정에 있는 신규 글 시간 > 현재 시간 --}}
                            @if(date($write->created_at->addHours(24)) > date("Y-m-d H:i:s", time()) && $board->new != 0 )
                            <img src="/themes/default/images/icon_new.gif"> <!-- 새글 -->
                            @endif
                            <!-- 인기글 -->
                            @if($write->hit >= $board->hot)
                            <img src="/themes/default/images/icon_hot.gif"> <!-- 인기 -->
                            @endif
                            @if($write->comment > 0)
                            <span class="bd_cmt">{{ $write->comment }}</span>
                            @endif
                        </span>
                    </p>
                    <span>
                    @if(auth()->user() && $board->use_sideview)
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">{{ $write->name }}</a>
                        <ul class="dropdown-menu" role="menu">
                        @if($write->user_level)
                            @component('board.sideview', ['id' => $write->user_id, 'name' => $write->name, 'email' => $write->email])
                            @endcomponent
                            <li><a href="/board/{{ $board->id }}?kind=user_id&amp;keyword={{ $write->user_id }}&amp;category={{ $currenctCategory }}">이 회원이 작성한 글</a></li>
                        @else
                            <li><a href="/board/{{ $board->id }}?kind=name&amp;keyword={{ $write->name }}&amp;category={{ $currenctCategory }}">이름으로 검색</a></li>
                        @endif
                        @if($write->user_level)
                            <li><a href="{{ route('new.index') }}?nick={{ $write->name }}">전체게시물</a></li>
                        @endif
                        </ul>
                    @elseif(auth()->guest() && $board->use_sideview)
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">{{ $write->name }}</a>
                        <ul class="dropdown-menu" role="menu">
                        @if($write->user_level)
                            @component('board.sideview', ['id' => $write->user_id, 'name' => $write->name, 'email' => $write->email])
                            @endcomponent
                            <li><a href="/board/{{ $board->id }}?kind=user_id&amp;keyword={{ $write->user_id }}&amp;category={{ $currenctCategory }}">이 회원이 작성한 글</a></li>
                        @else
                            <li><a href="/board/{{ $board->id }}?kind=name&amp;keyword={{ $write->name }}&amp;category={{ $currenctCategory }}">이름으로 검색</a></li>
                        @endif
                        @if($write->user_level)
                            <li><a href="{{ route('new.index') }}?nick={{ $write->name }}">전체게시물</a></li>
                        @endif
                        </ul>
                    @else
                        {{ $write->name }}
                    @endif
                    </span>
                    <span><i class="fa fa-clock-o"></i>@monthAndDay($write->created_at)</span>
                    <span><i class="fa fa-clock-o"></i>{{ $write->hit }}</span><br>
                    @if($board->use_good)
                    <span><i class="fa fa-thumbs-up"></i>{{ $write->good }}</span>
                    @endif
                    @if($board->use_nogood)
                    <span><i class="fa fa-thumbs-down"></i>{{ $write->nogood }}</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
        @else
            <div class="gry_empty_table">
                <span class="empty_table">
                    <i class="fa fa-exclamation-triangle"></i> 게시물이 없습니다.
                </span>
            </div>
        @endif
    </div>
</form>

<div class="bd_btn">
    <ul class="pull-left">
        <li id="pt_sch">
            <form method="get" action="{{ route('board.index', $board->id) }}" onsubmit="return searchFormSubmit(this);">
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
                <input type="text" name="keyword" id="keyword" value="{{ $keyword }}" class="search" required>
                <button type="submit" class="search-icon">
                    <i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">검색</span>
                </button>
            </form>
        </li>
    </ul>

    <ul id="bd_btn">
        <li class="mr0">
            @if($board->use_rss_view && $board->list_level == 1 && $board->read_level == 1)
            <button type="button" class="btn btn-sir" onclick="location.href='{{ route('rss', $board->id) }}'">
                RSS
            </button>
            @endif
            <button type="button" class="btn btn-sir" onclick="location.href='{{ route('board.create', $board->id). '?'. $request->getQueryString() }}'">
                <i class="fa fa-pencil"></i> 글쓰기
            </button>
        </li>
    </ul>
</div>

{{-- 페이지 처리 --}}
{{ $writes->appends(Request::except('page'))->links() }}

<script>
$(function(){
    var category = "{{ $currenctCategory }}";
    if(category != '') {
        // document.getElementById(category).addClass
        // $("div[id='" + category + "']'").addClass('on');
        document.getElementById(category).className += ' on'
    } else {
        document.getElementById('all').className += ' on'
    }
});

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
        f.action = '/board/{{ $board->id }}/delete/ids/' + selected_id_array;
        $('#_method').val('DELETE');
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
    f.action = "{{ route('board.list.move', $board->id)}}";
    f.submit();
}
</script>