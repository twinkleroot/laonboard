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

	<div class="clearfix">
		<div class="pull-left bd_head">
			<span><a href="{{ route('board.index', $board->id) }}">{{ $board->subject }}</a> 전체 {{ $writes->total() }}건 {{ $writes->currentPage() }}페이지</span>
		</div>

		<ul id="bd_btn" class="pull-right">
			<li class="dropdown">
                @if($userLevel == 10)
				<a href="#" class="dropdown-toggle bd_rd_more" data-toggle="dropdown" role="button" aria-expanded="false">
					<button type="" class="btn btn-danger">
						<i class="fa fa-cog"></i> 관리
					</button>
				</a>
                @endif
				<ul class="dropdown-menu" role="menu">
	                <li><a href="#">선택삭제</a></li>
	                <li><a href="#">선택복사</a></li>
	                <li><a href="#">선택이동</a></li>
	                <li><a href="{{ route('admin.boards.edit', $board->id) }}">게시판 설정</a></li>
	            </ul>
			</li>

			<li class="mr0">
				<button type="" class="btn btn-sir">
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
                @if($userLevel == 10)
    				<th> <!-- 전체선택 -->
    					<input type="checkbox" name="">
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
			<tr id="bd_notice">
                <!-- 공지사항 기능 넣으면 공지사항 까지 포함시켜서 넘버링 -->
                {{-- <td class="bd_num">{{ $writes->total() - ( $write->currentPage() - 1 )*$board->page_rows - $noticeCount - $loop->index }}</td> --}}
				<td class="bd_num">{{ $writes->total() - ( $writes->currentPage() - 1 ) * $board->page_rows - $loop->index }}</td>
                @if($userLevel == 10)
    				<td class="bd_check"><input type="checkbox" name=""></td>
                @endif
				<td>
					<span class="bd_subject">
                        <a href="{{ route('board.show', ['boardId' => $board->id, 'postId' => $write->id]) }}">{{ $write->subject }}</a>
                    </span>
                    @if($write->comment > 0)
                        <span class="bd_cmt">{{ $write->comment }}</span>
                    @endif
				</td>
				<td class="bd_name">{{ $write->author }}</td>
				<td class="bd_date">@monthAndDay($write->created_at)</td>
				<td class="bd_hits">{{ $write->hit }}</td>
				<td class="bd_re"><span class="up">{{ $write->good }}</span></td>
				<td class="bd_nre">{{ $write->nogood }}</td>
			</tr>
            @endforeach
			{{-- <tr>
				<td class="bd_num">4</td>
				<td class="bd_check"><input type="checkbox" name=""></td>
				<td>
					<span class="bd_subject">새글이당</span>
						<img src="../assets/images/icon_new.gif"> <!-- 새글 -->
						<img src="../assets/images/icon_file.gif"> <!-- 파일 -->
						<img src="../assets/images/icon_link.gif"> <!-- 링크 -->
					<span class="bd_cmt">8</span>
				</td>
				<td class="bd_name">최고관리자</td>
				<td class="bd_date">04-07</td>
				<td class="bd_hits">5</td>
				<td class="bd_re"><span class="up">2</span></td>
				<td class="bd_nre">1</td>
			</tr> --}}

		</tbody>
	</table>

	<div class="mb10 clearfix">
		<ul id="bd_btn" class="pull-left">
			<li id="pt_sch">
				<form method="get" action="{{ route('board.index', $board->id) }}">
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
				<button type="" class="btn btn-sir"><i class="fa fa-pencil"></i> 글쓰기</button>
			</li>
		</ul>
	</div>

    {{-- 페이지 처리 --}}
    {{ str_contains(url()->current(), 'search')
        ? $writes->appends([
            'admin_page' => 'post',
            'kind' => $kind,
            'keyword' => $keyword,
        ])->links()
        : $writes->links()
    }}
</div>

@endsection
