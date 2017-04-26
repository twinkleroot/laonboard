@extends('theme')

@section('title')
    {{ $board->subject }} 게시글 작성
@endsection

@section('content')
@if(Session::has('message'))
    <div class="alert alert-info">
    {{Session::get('message') }}
    </div>
@endif
<!-- Board start -->
<div id="board" class="container">

    <!-- 게시글 작성 -->
    <form role="form" method="post" action={{ route('board.store', $board->id )}}>
        {{ csrf_field() }}
        @if(is_null(Auth::user()))
        <div class="nologin"> <!-- 추가된 부분 -->
    		<div class="form-group mb10 row">
    			<div class="col-xs-3">
    				<label for="name" class="sr-only">이름</label>
    				<input type="text" class="form-control" id="name" name="name" placeholder="이름">
    			</div>
    		</div>
    		<div class="form-group mb10 row">
    			<div class="col-xs-4">
    				<label for="password" class="sr-only">비밀번호</label>
    				<input type="password" class="form-control" id="password" name="password" placeholder="비밀번호">
    			</div>
    		</div>
    		<div class="form-group mb10 row">
    			<div class="col-xs-5">
    				<label for="email" class="sr-only">이메일</label>
    				<input type="email" class="form-control" id="email" name="email" placeholder="이메일">
    			</div>
    		</div>
    		<div class="form-group mb10 row">
    			<div class="col-xs-5">
    				<label for="homepage" class="sr-only">홈페이지</label>
    				<input type="text" class="form-control" id="homepage" name="homepage" placeholder="홈페이지">
    			</div>
    		</div>
    		<div class="form-group mb10 row">
    			<div class="col-xs-3">
    				<select class="form-control">
    					<option>분류</option>
    				</select>
    			</div>
    		</div>
    	</div> <!-- 추가된 부분 END -->
        @endif

    	<div class="row">
    		<div class="form-group mb10 col-xs-8">
    		    <label for="" class="sr-only">게시물 작성</label>
    		    <input type="text" class="form-control" id="subject" name="subject" placeholder="게시물 제목">
    		</div>

    		<div class="bd-save col-xs-4 dropdown">
    			<a href="#" class="dropdown-toggle btn btn-sir" data-toggle="dropdown" role="button" aria-expanded="false">
    				<i class="fa fa-archive"></i>
    				<span style="margin-left: 5px">(1)</span>
    			</a>
    			<ul class="dropdown-menu" role="menu">
    	            <li>
    	                <a href="#">
    	                	<span>제목은 10바이트 표시</span>
    	                	<span class="sv-date">17-04-21</span>
    	                </a>
    	                <a href="#" class="save-delete"><i class="fa fa-times"></i></a> <!-- 임시저장글 삭제 -->
    	            </li>
    	        </ul>
    		</div>
    	</div>

    	<div class="mb10" style="border: 1px solid #ccc; background: #fff; min-height: 400px; padding: 20px; border-radius: 4px; box-sizing: border-box;">
            @if($board->use_dhtml_editor == 1)
              {{-- 에디터 --}}
            @else
                <textarea name='content' maxlength='65536' style='width:100%; min-height:400px'></textarea>
            @endif
    	</div>

    	<div class="row">
    		<div class="form-group mb10 col-xs-8">
    		    <label for="" class="sr-only">링크 1</label>
    		    <input type="email" class="form-control" id="" placeholder="링크 1">
    		</div>

    		<div class="form-group mb10 col-xs-8">
    		    <label for="" class="sr-only">링크 2</label>
    		    <input type="email" class="form-control" id="" placeholder="링크 2">
    		</div>

    		<div class="form-group mb10 col-xs-5">
    		    <label for="" class="sr-only">파일첨부</label>
    		    <input type="file" id="" placeholder="파일첨부">
    		</div>
    	</div>

    	<div class="clearfix">
    		<div class="pull-left">
    			<label class="checkbox-inline">
    				<input type="checkbox" id="" value="option1"> 공지
    			</label>
    			<label class="checkbox-inline">
    				<input type="checkbox" id="" value="option2"> html
    			</label>
    			<label class="checkbox-inline">
    				<input type="checkbox" id="" value="option3"> 답변메일받기
    			</label>
    		</div>
    		<div class="pull-right">
    			<button type="submit" class="btn btn-sir">작성완료</button>
    			<button type="button" class="btn btn-default" onclick="history.back();">취소</button>
    		</div>
    	</div>
    </form>
@endsection
