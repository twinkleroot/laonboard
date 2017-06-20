<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SIR LaBoard')</title>

    <!-- css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('font-awesome/css/font-awesome.css') }}">
    @yield('include_css')

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>

    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    @yield('include_script')
</head>
<body>

<div id="admin-header">
	<div class="header-title sidebarmenu">
		<h1><i class="fa fa-cogs"></i>Administrator</h1>
	</div>

	<div class="box-left sidebarmenu">
		<div class="hdbt bt-menu" id="showmenu">
			<i class="fa fa-bars"></i>
			<span>menu</span>
		</div>
		<div class="hdbt bt-home">
			<a href="{{ url('/') }}">
				<i class="fa fa-home"></i>
				<span>home</span>
			</a>
		</div>
	</div>

	<div class="box-right pull-right">
		<div class="hdtx">
			<ul>
				<li>
					<a href="#">부가서비스</a>
				</li>
			</ul>
		</div>
		<div class="hdbt bt-user">
			<i class="fa fa-user"></i>
			<span class="sign">{{ Auth::user()->nick }}</span>
		</div>
	</div>
</div>

<div class="sidebar sidebarmenu">
	<ul class="category">
        <div class="side_1depth">
		    <a href="#" class="sd_1depth">환경설정</a>
		</div>
		<ul class="sd_2depth">
			<li><a href="{{ route('admin.config')}}">기본환경설정</a></li>
		    <li><a href="#">관리권한설정</a></li>
	        <li><a href="#">테마설정</a></li>
	        <li><a href="{{ route('admin.menus.index') }}">메뉴설정</a></li>
	        <li><a href="{{ route('admin.email') }}">메일테스트</a></li>
	        <li><a href="#">팝업레이어관리</a></li>
	        <li><a href="#">세션파일 일괄삭제</a></li>
	        <li><a href="#">캐시파일 일괄삭제</a></li>
	        <li><a href="#">썸네일파일 일괄삭제</a></li>
	        <li><a href="{{ route('admin.phpinfo') }}">phpinfo()</a></li>
	        <li><a href="{{ route('admin.extra_service') }}">부가서비스</a></li>
		</ul>
	</ul>
	<ul class="category">
        <div class="side_1depth">
		    <a href="#" class="sd_1depth">회원관리</a>
		</div>
		<ul class="sd_2depth">
			<li><a href="{{ route('admin.users.index') }}">회원관리</a></li>
		    <li><a href="{{ route('admin.points.index') }}">포인트관리</a></li>
		</ul>
	</ul>
	<ul class="category">
        <div class="side_1depth">
		    <a href="#" class="sd_1depth">게시판관리</a>
		</div>
		<ul class="sd_2depth">
			<li><a href="{{ route('admin.boards.index') }}">게시판관리</a></li>
		    <li><a href="{{ route('admin.groups.index') }}">게시판그룹관리</a></li>
	        <li><a href="#">인기검색어관리</a></li>
	        <li><a href="#">인기검색어순위</a></li>
	        <li><a href="{{ route('contents.index') }}">내용관리</a></li>
	        <li><a href="#">글/댓글현황</a></li>
		</ul>
	</ul>
</div>

<div id="admin-body" class="sidebarmenu2">
	<div class="admin-body">
	@yield('content')
	</div>
</div>

<script>
$(document).ready(function() {
    $('#showmenu').click(function() {
    var hidden = $('.sidebarmenu').data('hidden');
    if(hidden){
        $('.sidebarmenu').animate({
            left: '0px'
        },300),
        $('.sidebarmenu2').animate({
            left: '230px'
        },300)
    } else {
        $('.sidebarmenu').animate({
            left: '-230px'
        },300),
        $('.sidebarmenu2').animate({
            left: '0px'
        },300)
    }
    $('.sidebarmenu,.image').data("hidden", !hidden);
    });
});

$(document).ready(function() {
    $('.sd_2depth').hide();
        $('a.sd_1depth').click(function() {
            $(this).parent().next('.sd_2depth').toggle(200);
            return false;
        });
});
</script>

<!-- Bootstrap core JavaScript -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="{{ asset('bootstrap/js/ie10-viewport-bug-workaround.js') }}"></script>
</body>
</html>
