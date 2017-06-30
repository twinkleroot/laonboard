<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', '라온보드')</title>

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

        function alertclose() {
	        document.getElementById("adm_save").style.display = "none";
	    }
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
			<i class="fa fa-outdent"></i>
		</div><!--
		<div class="hdbt bt-home">
			<a href="{{ url('/') }}">
				<i class="fa fa-home"></i>
				<span>home</span>
			</a>
		</div>-->
	</div>

	<div class="box-right pull-right">
		<div class="hdtx">
			<ul>
				<li>
					<a href="{{ url('/') }}">
						<i class="fa fa-home"></i>
						<span>Home</span>
					</a>
				</li>
				<li>
					<a href="{{ route('admin.extra_service') }}">부가서비스</a>
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
	@foreach(session()->get(auth()->user()->id_hashkey.'_admin_primary_menu') as $key => $primaryMenu)
	<ul class="category">
        <div class="side_1depth">
		    <a href="#" class="sd_1depth">{{ $primaryMenu[0] }}</a>
		</div>
		@if(count(session()->get(auth()->user()->id_hashkey.'_admin_sub_menu')) > 0)
		<ul class="sd_2depth">
			@foreach(session()->get(auth()->user()->id_hashkey.'_admin_sub_menu') as $subMenuCode => $subMenu)
				@if(substr($key, 0, 1) == substr($subMenuCode, 0, 1))
				<li><a href="{{ $subMenu[1] ? route($subMenu[1]) : '' }}" id="{{ $subMenuCode }}">{{ $subMenu[0] }}</a></li>
				@endif
			@endforeach
		</ul>
		@endif
	</ul>
	@endforeach
</div>

<div id="admin-body" class="sidebarmenu2">
	<div class="admin-body">
	@yield('content')
	</div>
</div>

<div class="upbtn">
	<a href="#admin-header">
		<i class="fa fa-angle-up"></i>
	</a>
</div>

<script>
$(document).ready(function() {
	$("a[id='" + menuVal+ "']").parent().parent().show();
	$("a[id='" + menuVal+ "']").css('background', '#616161');

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

	$('a.sd_1depth').click(function() {
        $(this).parent().next('.sd_2depth').toggle(200);
        return false;
    });
});

$(document).ready(function(){
	$(".upbtn").hide(); //top버튼
	$(function () {
		$(window).scroll(function () {
			if ($(this).scrollTop() > 100) {
			$('.upbtn').fadeIn();
			} else {
			$('.upbtn').fadeOut();
			}
		});
		$('.upbtn a').click(function () {
			$('body,html').animate({
			scrollTop: 0
			}, 500);
			return false;
		});
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
