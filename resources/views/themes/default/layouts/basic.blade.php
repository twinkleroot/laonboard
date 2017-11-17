<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
@if(cache('config.homepage')->addMeta)
{!! cache('config.homepage')->addMeta !!}
@endif
<title>@yield('title', config('app.name'))</title>
@yield('fisrt_include_css')
<link rel="stylesheet" type="text/css" href="{{ ver_asset('bootstrap/css/bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('font-awesome/css/font-awesome.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/style.css') }}">
@yield('include_css')
@if(cache('config.homepage')->analytics)
{!! cache('config.homepage')->analytics !!}
@endif
<script src="{{ ver_asset('js/jquery-3.1.1.min.js') }}"></script>
<script src="{{ ver_asset('js/common.js') }}"></script>
<script>
    window.Laravel = {!! json_encode([
        'csrfToken' => csrf_token(),
    ]) !!};

    $(function(){
        $('.gnb-li.dropdown').hover(function() {
            $(this).addClass('open');
        }, function() {
            $(this).removeClass('open');
        });
    });
</script>

@yield('include_script')
</head>
<body>
@yield('headerUp')
<div id="header">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            {{ fireEvent('headerLefts') }}
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            {{ fireEvent('headerContents') }}
        </div>
    </div>
</div>
<div id="contents">
    @yield('content')
</div>
<footer id="footer">
    {{ fireEvent('footerUp') }}
    <div id="ft_copy">
        <div class="container">
            {{ fireEvent('footerContents') }}
        </div>
    </div>
</footer>

<div class="upbtn">
    <a href="#header">
        <i class="fa fa-angle-up"></i>
    </a>
</div>

<script type="text/javascript">
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

<script src="{{ ver_asset('bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ ver_asset('bootstrap/js/ie10-viewport-bug-workaround.js') }}"></script>
</body>
</html>
