@extends('theme')

@section('title')
    {{ $board->subject }} 리스트
@endsection

@section('content')
@if(Session::has('message'))
    <div class="alert alert-info">
    {{Session::get('message') }}
    </div>
@endif
<!-- Board start -->
<div id="board" class="container">

 <form name="fBoardList" id="fBoardList" action="" onsubmit="return formBoardListSubmit(this);" method="post" target="move">
    <input type="hidden" id='_method' name='_method' value='post' />
    <input type="hidden" id='type' name='type' value='' />
    {{ csrf_field() }}

	<div class="clearfix">
		<div class="pull-left bd_head">
			<span><a href="{{ route('board.index', $board->id) }}">{{ $board->subject }}</a> 전체 {{ $writes->total() }}건 {{ $writes->currentPage() }}페이지</span>
		</div>

		<ul id="bd_btn" class="pull-right">
            @if(session()->get('admin'))
    			<li class="dropdown">
    				<a href="#" class="dropdown-toggle bd_rd_more" data-toggle="dropdown" role="button" aria-expanded="false">
    					<button type="" class="btn btn-danger">
    						<i class="fa fa-cog"></i> 관리
    					</button>
    				</a>
    				<ul class="dropdown-menu" role="menu">
                        <li><input type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value"/></li>
    	                <li><input type="submit" name="btn_submit" value="선택복사" onclick="document.pressed=this.value"/></li>
                        <li><input type="submit" name="btn_submit" value="선택이동" onclick="document.pressed=this.value"/></li>
    	                <li><a href="{{ route('admin.boards.edit', $board->id) }}">게시판 설정</a></li>
    	            </ul>
    			</li>
            @endif

			<li class="mr0">
				<button type="button" class="btn btn-sir" onclick="location.href='{{ route('board.create', $board->id) }}'">
					<i class="fa fa-pencil"></i> 글쓰기
				</button>
			</li>
		</ul>
	</div>

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
            {{-- <tr id="bd_notice">
                <td>
                    <span class="bd_subject">공지사항테스트</span>
                        <img src="../themes/default/images/icon_new.gif"> <!-- 새글 -->
                        <img src="../themes/default/images/icon_file.gif"> <!-- 파일 -->
                        <img src="../themes/default/images/icon_link.gif"> <!-- 링크 -->
                        <img src="../assets/images/icon_link.gif"> <!-- 링크 -->
                    <span class="bd_cmt">8</span>
                </td>
            </tr> --}}
        @foreach($writes as $write)
            @if(in_array($write->id, $notices))
                <tr id="bd_notice">
            @else
                <tr>
            @endif
                <!-- 공지사항 기능 넣으면 공지사항 까지 포함시켜서 넘버링 -->
                {{-- <td class="bd_num">{{ $writes->total() - ( $write->currentPage() - 1 )*$board->page_rows - $noticeCount - $loop->index }}</td> --}}
				<td class="bd_num">{{ $writes->total() - ( $writes->currentPage() - 1 ) * $board->page_rows - $loop->index }}</td>
                @if(session()->get('admin'))
    				<td class="bd_check"><input type="checkbox" name="chk_id[]" class="writeId" value='{{ $write->id }}'></td>
                @endif
				<td>
					<span class="bd_subject">
                        <a href="{{ route('board.show', ['boardId' => $board->id, 'postId' => $write->id]) }}">{{ $write->subject }}</a>
                    </span>
                    @if($write->comment > 0)
                        <span class="bd_cmt">{{ $write->comment }}</span>
                    @endif
				</td>
				<td class="bd_name">
                    @if($userLevel == 1)
                        {{ $write->name }}
                    @else
                        <a href="/board/{{ $board->id }}?kind=user_id&amp;keyword={{ $write->user_id }}">{{ $write->name }}</a>
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

	<div class="mb10 clearfix">
		<ul id="bd_btn" class="pull-left">
			<li id="pt_sch">
				<form method="get" action="{{ route('board.index', $board->id) }}" onsubmit="return searchFormSubmit(this);">
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

	    <ul id="bd_btn" class="pull-right">
			<li class="mr0">
                <button type="button" class="btn btn-sir" onclick="location.href='{{ route('board.create', $board->id) }}'">
					<i class="fa fa-pencil"></i> 글쓰기
				</button>
			</li>
		</ul>
	</div>

    {{-- 페이지 처리 --}}
    {{ $search == 1
        ? $writes->appends([
            'kind' => $kind,
            'keyword' => $keyword,
        ]) ->links()
        : $writes->links()
    }}
</div>
<script>
// 모두 선택
function checkAll(form) {
    var chk = document.getElementsByName("chk_id[]");

    for (i=0; i<chk.length; i++) {
        chk[i].checked = form.chkAll.checked;
    }
}

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
        f.action = '/board/{{ $board->id }}/write/' + selected_id_array;
        $('#_method').val('DELETE');
    }

    return true;
}

// 선택한 항목들 id값 배열에 담기
function selectIdsByCheckBox(className) {
    var send_array = Array();
    var send_cnt = 0;
    var chkbox = $(className);

    for(i=0; i<chkbox.length; i++) {
        if(chkbox[i].checked == true) {
            send_array[send_cnt] = chkbox[i].value;
            send_cnt++;
        }
    }

    return send_array;
}

// 선택한 게시물 복사 및 이동
function selectCopy(type) {
    var f = document.fBoardList;

    if (type == "copy")
        str = "복사";
    else
        str = "이동";

    var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");

    f.type.value = type;
    f.target = "move";
    f.action = "{{ route('board.move', $board->id)}}";
    f.submit();
}
</script>
@endsection
