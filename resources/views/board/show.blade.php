@extends('theme')

@section('title')
    {{ $view->subject }} > {{ $board->subject }} | {{ App\Config::getConfig('config.homepage')->title }}
@endsection

@section('content')
{{-- <body style="background: #fff"> --}}

<div id="header">
</div>
<!-- Board start -->
<div id="board" class="container">

	<!-- 게시글 조회 -->
	<div class="bd_rd_head">
		<h1>{{ $view->subject }}</h1>
		<ul class="bd_rd_info">
			<li><i class="fa fa-user"></i>{{ $view->name }} @if($board->use_ip_view) ({{ $view->ip }}) @endif</li>
			<li><i class="fa fa-clock-o"></i>@datetime($view->created_at)</li>
			<li><i class="fa fa-eye"></i>{{ $view->hit }}</li>
		</ul>
		<ul class="bd_rd_btn pull-right">
			<li class="dropdown">
				<a href="#" class="dropdown-toggle bd_rd_more" data-toggle="dropdown" role="button" aria-expanded="false">
					<i class="fa fa-ellipsis-v"></i>
				</a>
				<ul class="dropdown-menu" role="menu">
	                <li><a href="#">수정</a></li>
	                <li><a href="#">삭제</a></li>
	                <li><a href="#">복사</a></li>
	                <li><a href="#">이동</a></li>
	                <li><a href="#">답변</a></li>
	            </ul>
			</li>
			<li>
				<a href="#">
					<a href="{{ route('board.index', $board->id) }}?page={{ $request->page }}"><i class="fa fa-list-ul"></i></a>
				</a>
			</li>
			<li>
				<a href="#">
					<a href="{{ route('board.create', $board->id) }}"><i class="fa fa-pencil"></i></a>
				</a>
			</li>
		</ul>
	</div>
	<div class="bd_rd">
        {!! $view->content !!}
	</div>

    @if($board->use_signature)
        <div class="bd_rd">
            {{ auth()->user()->signature }}
        </div>
    @endif

	<div class="bd_rd_bt clearfix">
		<p class="pull-left bd_rd_cmthd">댓글 2개</p>
		<ul class="pull-right bd_rd_count">
			<li>
				<i class="fa fa-heart"></i>
				<span class="bd_rd_bt_txt">스크랩</span>
				<span class="bd_rd_bt_count">0</span>
			</li>
            @if($board->use_good)
        		<li>
        			<i class="fa fa-thumbs-o-up"></i>
        			<span class="bd_rd_bt_txt">추천</span>
        			<span class="bd_rd_bt_count">0</span>
        		</li>
            @endif
            @if($board->use_nogood)
    			<li>
    				<i class="fa fa-thumbs-o-down"></i>
    				<span class="bd_rd_bt_txt">비추천</span>
    				<span class="bd_rd_bt_count">0</span>
    			</li>
            @endif
		</ul>
	</div>

	<section id="bd_rd_cmt">
		<article class="cmt">
			<!-- 답글일 경우 추가
			<div class="cmt_reply pull-left">
				<i class="fa fa-reply fa-rotate-180"></i>
			</div>
			 답글일 경우 추가 END -->
			<div>
				<div class="clearfix">
					<ul class="bd_rd_cmt_info pull-left">
						<li><i class="fa fa-user"></i>최고관리자 (106.245.92.30)</li>
						<li><i class="fa fa-clock-o"></i>17-03-29 11:33</li>
					</ul>

					<ul class="bd_rd_cmt_info pull-right">
						<li>답글</li>
						<li>수정</li>
						<li>삭제</li>
					</ul>
				</div>
				<div class="bd_rd_cmt_view">
					<p>작성한 코멘트<br>작성한 코멘트<br>작성한 코멘트<br>작성한 코멘트<br>작성한 코멘트<br>작성한 코멘트</p>
				</div>
			</div>
		</article>
		<article class="cmt">
			<!-- 답글일 경우 추가 -->
			<div class="cmt_reply pull-left">
				<i class="fa fa-reply fa-rotate-180"></i>
			</div>
			<!-- 답글일 경우 추가 END -->
			<div>
				<div class="clearfix">
					<ul class="bd_rd_cmt_info pull-left">
						<li><i class="fa fa-user"></i>최고관리자 (106.245.92.30)</li>
						<li><i class="fa fa-clock-o"></i>17-03-29 11:33</li>
					</ul>

					<ul class="bd_rd_cmt_info pull-right">
						<li>답글</li>
						<li>수정</li>
						<li>삭제</li>
					</ul>
				</div>
				<div class="bd_rd_cmt_view">
					<p>작성한 코멘트<br>작성한 코멘트<br>작성한 코멘트<br>작성한 코멘트<br>작성한 코멘트<br>작성한 코멘트</p>
				</div>
			</div>
		</article>
	</section>

	<form class="cmt_write">
		<div class="form-inline info_user">
			<div class="form-group"> <!-- 비회원일경우 노출 -->
			    <label for="" class="sr-only">이름</label>
			    <input type="text" class="form-control" id="" placeholder="이름">
			</div>

			<div class="form-group">
			    <label for="" class="sr-only">비밀번호</label>
			    <input type="password" class="form-control" id="" placeholder="비밀번호">
			</div> <!-- 비회원일경우 노출 END -->

			<div class="form-group checkbox">
			    <label>
				   	<input type="checkbox"><span>비밀글 사용</span>
				</label>
			</div>
		</div>

		<textarea class="form-control" rows="4" placeholder="덧글을 입력해 주세요."></textarea>


	    <div class="row clearfix">
	    	<!-- 리캡챠 -->

			<div class="pull-right col-md-3">
				<button type="submit" class="btn btn-sir btn-block btn-lg">댓글등록</button>
			</div>
		</div>
	</form>
</div>
@if($board->use_list_view)
    @include('board.list')
@endif
<script>
    $(function(){
        $('body').css('background', '#fff');
    });
</script>
@endsection
