<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SIR LaBoard')</title>

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/common.css') }}">
    <link rel="stylesheet" type="text/css" href="http://fontawesome.io/assets/font-awesome/css/font-awesome.css">
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
<!-- header -->
<div id="header">
<div class="container">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        <!-- logo -->
        <a class="logo" href="{{ url('/') }}">
            <img src="{{ asset('themes/default/images/logo2.png') }}">
        </a>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
        <!-- menu -->
        <ul class="gnb navbar-nav">
            
            <!-- 일반메뉴 
            <li class="gnb-li"><a href="">메뉴1</a></li>
            <li class="gnb-li"><a href="">메뉴2</a></li>-->

            @if (Auth::guest()) <!-- 공개권한: 게스트 -->
            <li class="gnb-li"><a href="{{ route('login') }}">로그인</a></li>
            <li class="gnb-li"><a href="{{ route('register') }}">회원가입</a></li>
            @else <!-- else -->
                @if(Auth::user()->level == 10) <!-- 공개권한: 관리자 -->
                    <li class="gnb-li"><a href="{{ route('admin.config') }}">환경설정</a></li>
                    <li class="gnb-li"><a href="{{ route('users.index') }}">회원관리</a></li>
                @endif <!-- 공개권한: 관리자 end -->

                <!-- 공개권한: 회원 -->
                <li class="gnb-li dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        {{ Auth::user()->nick }} <span class="caret"></span>
                    </a>
                    <!-- 2depth -->
                    <ul class="dropdown-menu" role="menu">

                        @if(Auth::user()->level < 10)
                            <li><a href="{{ route('user.checkPassword') }}">회원 정보 수정</a></li>
                        @endif
                        <li><a href="{{ route('board.index') }}">자유게시판</a></li>
                        <li>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                로그아웃
                            </a>
                            <!-- 로그아웃 토큰 -->
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </li>
                <!-- 공개권한: 회원 end -->
            @endif <!-- 공개권한: 게스트 end -->
        </ul>
    </div>
</div>
</div>

<!-- contents -->
<div id="contents">
    @yield('content')
</div>

<footer id="footer">
    <div class="container">
        Copyright 2017 SIR. All Rights Reserved.
    </div>
</footer>

<!-- Bootstrap core JavaScript -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="http://bootstrapk.com/dist/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="http://bootstrapk.com/assets/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>