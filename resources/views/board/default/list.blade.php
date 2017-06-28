@section('fisrt_include_css')
    <link rel="alternate" type="application/rss+xml" href="{{ url('rss') }}"
        title="RSS Feed {{ config('rss.title') }}">
@endsection

<!-- Board list start -->

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
				<button type="button" class="btn btn-sir" onclick="location.href='{{ route('board.create', $board->id) }}'">
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

	<!-- 리스트형 게시판 -->
	<table class="table box">
		<thead>
			<tr>
				<th>번호</th>
                @if(session()->get('admin'))
    				<th> <!-- 전체선택 -->
    					<input type="checkbox" name="chkAll" onclick="checkAll(this.form)">
    				</th>
                @endif
				<th>제목</th>
				<th>글쓴이</th>
				<th>날짜</th>
				<th>조회</th>
				<th>추천</th>
				<th>비추천</th>
			</tr>
		</thead>
		<tbody>
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
                @if(session()->get('admin'))
    				<td class="bd_check"><input type="checkbox" name="chkId[]" class="writeId" value='{{ $write->id }}'></td>
                @endif
				<td @if($write->reply != '') class="bd_reply" style="padding-left: calc(20px * {{ strlen($write->reply) }} @endif">

					<span class="bd_subject">
                        @if($board->use_category == 1 )
                            <a href="{{ route('board.index', $board->id). '?category='. $write->ca_name }}">{{ $write->ca_name }}</a>
                        @endif
                        @if($viewParams == '')
                            <a href="/board/{{ $board->id }}/view/{{ $write->id }}">{{ $write->subject }}</a>
                        @else
                            <a href="/board/{{ $board->id }}/view/{{ $write->id }}?{{ $viewParams }}">
                                {!! $write->subject !!}
                            </a>
                        @endif
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
                        <!-- 인기글 -->
                        @if($write->hit >= $board->hot)
                            <img src="/themes/default/images/icon_hot.gif"> <!-- 인기 -->
                        @endif
                        <!-- 비밀글 -->
                        @if(str_contains($write->option, 'secret'))
                            <img src="/themes/default/images/icon_secret.gif"> <!-- 비밀 -->
                        @endif
                    </span>
                    @if($write->comment > 0)
                        <span class="bd_cmt">{{ $write->comment }}</span>
                    @endif
				</td>
				<td class="bd_name">
                    @if(auth()->user() && $board->use_sideview)
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">{{ $write->name }}</a>
                        <ul class="dropdown-menu" role="menu">
                        @if($write->user_level)
                            <li><a href="{{ route('memo.create') }}?to={{ $write->user_id_hashkey }}" class="winMemo" target="_blank" onclick="winMemo(this.href); return false;">쪽지보내기</a></li>
                            <li><a href="#">메일보내기</a></li>
                            <li><a href="{{ route('user.profile', $write->user_id_hashKey) }}" class="winProfile" target="_blank" onclick="winProfile(this.href); return false;">자기소개</a></li>
                            @if(session()->get('admin'))
        		                <li><a href="{{ route('admin.users.edit', $write->user_id_hashKey) }}" target="_blank">회원정보변경</a></li>
        		                <li><a href="{{ route('admin.points.index') }}?kind=email&amp;keyword={{ $write->user_email }}" target="_blank">포인트내역</a></li>
                            @endif
                        @endif
                            <li>
                            @if($currenctCategory!='')
                                <a href="/board/{{ $board->id }}?category={{ $currenctCategory }}&amp;kind=user_id&amp;keyword={{ $write->user_id }}">
                            @else
                                <a href="/board/{{ $board->id }}?kind=user_id&amp;keyword={{ $write->user_id }}">
                            @endif
                                닉네임으로 검색
                                </a>
                            </li>
                        @if($write->user_level)
                            <li><a href="{{ route('new.index') }}?nick={{ $write->name }}">전체게시물</a></li>
                        @endif
                        </ul>
                    @elseif($board->use_sideview)
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">{{ $write->name }}</a>
                        <ul class="dropdown-menu" role="menu">
                            @if($write->user_level)
                                <li>
                                @if($currenctCategory!='')
                                    <a href="/board/{{ $board->id }}?category={{ $currenctCategory }}&amp;kind=user_id&amp;keyword={{ $write->user_id }}">
                                @else
                                    <a href="/board/{{ $board->id }}?kind=user_id&amp;keyword={{ $write->user_id }}">
                                @endif
                                    닉네임으로 검색
                                    </a>
                                </li>
                            @else
                                <li>
                                @if($currenctCategory!='')
                                    <a href="/board/{{ $board->id }}?category={{ $currenctCategory }}&amp;kind=user_id&amp;keyword={{ $write->user_id }}">
                                @else
                                    <a href="/board/{{ $board->id }}?kind=name&amp;keyword={{ $write->name }}">
                                @endif
                                    이름으로 검색
                                    </a>
                                </li>
                            @endif
                            @if($write->user_level)
                                <li><a href="{{ route('new.index') }}?nick={{ $write->name }}">전체게시물</a></li>
                            @endif
                        </ul>
                    @endif
                </td>
				<td class="bd_date">@monthAndDay($write->created_at)</td>
				<td class="bd_hits">{{ $write->hit }}</td>
				<td class="bd_re"><span class="up">{{ $write->good }}</span></td>
				<td class="bd_nre">{{ $write->nogood }}</td>
			</tr>
        @endforeach
		</tbody>
	</table>
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
                    <option value="email, 0" @if($kind == 'email, 0') selected @endif>회원이메일</option>
                    <option value="email, 1" @if($kind == 'email, 1') selected @endif>회원이메일(코)</option>
                    <option value="nick, 0" @if($kind == 'nick, 0') selected @endif>글쓴이</option>
                    <option value="nick, 1" @if($kind == 'nick, 1') selected @endif>글쓴이(코)</option>
                </select>

                <label for="keyword" class="sr-only">검색어</label>
                <input type="text" name="keyword" id="keyword" value="" class="search" required>
                <button type="submit" id="" class="search-icon">
                    <i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">검색</span>
                </button>
            </form>
        </li>
    </ul>

    <ul id="bd_btn">
        <li class="mr0">
            <button type="button" class="btn btn-sir" onclick="location.href='{{ route('board.create', $board->id) }}'">
                <i class="fa fa-pencil"></i> 글쓰기
            </button>
        </li>
    </ul>
</div>

{{-- 페이지 처리 --}}
@if($search == 1 && $currenctCategory != '')
    {{ $writes->appends([
        'category' => $currenctCategory,
        'kind' => $kind,
        'keyword' => $keyword,
    ]) ->links() }}
@elseif($search != 1 && $currenctCategory != '')
    {{ $writes->appends([
        'category' => $currenctCategory,
    ]) ->links() }}
@elseif($search == 1 && $currenctCategory == '')
    {{ $writes->appends([
        'kind' => $kind,
        'keyword' => $keyword,
    ]) ->links() }}
@else
    {{ $writes->links() }}
@endif

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
