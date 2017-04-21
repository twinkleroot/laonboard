@extends('theme')

@section('title')
    {{ $board->table_name }} 리스트
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
			<span>전체 4건 1페이지</span>
		</div>

		<ul id="bd_btn" class="pull-right">
			<li class="dropdown">
				<a href="#" class="dropdown-toggle bd_rd_more" data-toggle="dropdown" role="button" aria-expanded="false">
					<button type="" class="btn btn-danger">
						<i class="fa fa-cog"></i> 관리
					</button>
				</a>
				<ul class="dropdown-menu" role="menu">
	                <li><a href="#">선택삭제</a></li>
	                <li><a href="#">선택복사</a></li>
	                <li><a href="#">선택이동</a></li>
	                <li><a href="#">게시판 설정</a></li>
	            </ul>
			</li>
			<li class="mr0">
				<button type="" class="btn btn-sir">
					<i class="fa fa-pencil"></i> 글쓰기
				</button>
			</li>
		</ul>
	</div>

	<!-- 갤러리형 게시판 -->
	<input type="checkbox" name=""> <!-- 전체선택 -->
	<div id="gry" class="row">
		<div class="col-md-3 col-sm-6 col-xs-12 gry"> <!-- 한줄에 4개 배치 -->
			<input type="checkbox" name="">
			<div>
				<div class="gry_img"> <!-- height 기본값 150px로 css처리 해둠 -->
					<img src="https://file.namu.moe/file/%ED%8C%8C%EC%9D%BC%3Av7NtafG.png">
				</div>
				<div class="gry_info">
					<p>게시물 제목을 입력하세요</p>
					<span><i class="fa fa-user"></i>최고관리자</span>
					<span><i class="fa fa-clock-o"></i>17-04-11</span>
				</div>
			</div>
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12 gry"> <!-- 한줄에 4개 배치 -->
			<input type="checkbox" name="">
			<div>
				<div class="gry_img"> <!-- height 기본값 150px로 css처리 해둠 -->
					<img src="https://file.namu.moe/file/%ED%8C%8C%EC%9D%BC%3Av7NtafG.png">
				</div>
				<div class="gry_info">
					<p>게시물 제목을 입력하세요</p>
					<span><i class="fa fa-user"></i>최고관리자</span>
					<span><i class="fa fa-clock-o"></i>17-04-11</span>
				</div>
			</div>
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12 gry"> <!-- 한줄에 4개 배치 -->
			<input type="checkbox" name="">
			<div>
				<div class="gry_img"> <!-- height 기본값 150px로 css처리 해둠 -->
					<img src="https://file.namu.moe/file/%ED%8C%8C%EC%9D%BC%3Av7NtafG.png">
				</div>
				<div class="gry_info">
					<p>게시물 제목을 입력하세요</p>
					<span><i class="fa fa-user"></i>최고관리자</span>
					<span><i class="fa fa-clock-o"></i>17-04-11</span>
				</div>
			</div>
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12 gry"> <!-- 한줄에 4개 배치 -->
			<input type="checkbox" name="">
			<div>
				<div class="gry_img"> <!-- height 기본값 150px로 css처리 해둠 -->
					<img src="https://file.namu.moe/file/%ED%8C%8C%EC%9D%BC%3Av7NtafG.png">
				</div>
				<div class="gry_info">
					<p>게시물 제목을 입력하세요</p>
					<span><i class="fa fa-user"></i>최고관리자</span>
					<span><i class="fa fa-clock-o"></i>17-04-11</span>
				</div>
			</div>
		</div>
	</div>

	<div class="mb10 clearfix">
		<ul id="bd_btn" class="pull-left">
			<li id="pt_sch">
				<form>
			        <label for="" class="sr-only">검색대상</label>
					<select name="" id="">
						<option value="">제목</option>
						<option value="">내용</option>
						<option value="">제목+내용</option>
						<option value="">회원이메일</option>
						<option value="">회원이메일(코)</option>
						<option value="">글쓴이</option>
						<option value="">글쓴이(코)</option>
					</select>

				    <label for="" class="sr-only">검색어</label>
				    <input type="text" name="" value="" id="" class="search" required>
				    <button type="submit" id="" class="search-icon">
				    	<i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">검색</span>
				    </button>
		    	</form>
			</li>
		</ul>

	    <ul id="bd_btn" class="pull-right">
			<li class="mr0">
				<button type="" class="btn btn-sir">
					<i class="fa fa-pencil"></i> 글쓰기
				</button>
			</li>
		</ul>
	</div>

	<ul class="pagination">
		<li><a href="#"><</a></li>
	    <li><a href="#">1</a></li>
	    <li><a href="#">2</a></li>
	    <li><a href="#">3</a></li>
	    <li><a href="#">4</a></li>
	    <li><a href="#">5</a></li>
	    <li><a href="#">6</a></li>
	    <li><a href="#">7</a></li>
	    <li><a href="#">8</a></li>
	    <li><a href="#">9</a></li>
	    <li><a href="#">></a></li>
	</ul>
</div>

<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="http://bootstrapk.com/dist/js/bootstrap.min.js"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="http://bootstrapk.com/assets/js/ie10-viewport-bug-workaround.js"></script>
@endsection
