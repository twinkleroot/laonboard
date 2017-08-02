<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', '라온보드')</title>

    <!-- CSS -->
    @yield('fisrt_include_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('font-awesome/css/font-awesome.css') }}">
    @yield('include_css')

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('js/common.js') }}"></script>
    <script>
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
<!-- header -->
<div id="header">
    @yield('popup')
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
        <!-- 사이트 내 전체검색 -->
        <form class="hd_sch" name="searchBox" method="get" action="{{ route('search')}}" onsubmit="">
            <input type="hidden" name="kind" value="subject||content" />
            <fieldset>
                <legend>사이트 내 전체검색</legend>
                <label for="keyword" class="sr-only">검색어 필수</strong></label>
                <input type="text" name="keyword" id="keyword" maxlength="20" required>
                <input type="submit" id="searchSubmit" value="검색">
            </fieldset>
            <input type="hidden" name="operator" value="and" />
        </form>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
        <!-- menu -->
        <ul class="gnb navbar-nav">
        @for($i=0; $i<count(Cache::get('menuList')); $i++)
        @if(count(Cache::get('subMenuList')[$i]) > 0)
            <li class="gnb-li dropdown">
                <a href="{{ Cache::get('menuList')[$i]['link'] }}" role="button" aria-expanded="false">
                    {{ Cache::get('menuList')[$i]['name'] }}<span class="caret"></span>
                </a>
                <ul class="dropdown-menu" role="menu">
                @for($j=0; $j<count(Cache::get('subMenuList')[$i]); $j++)
                    <li><a href="{{ Cache::get('subMenuList')[$i][$j]['link'] }}">{{ Cache::get('subMenuList')[$i][$j]['name'] }}</a></li>
                @endfor
                </ul>
        @else
            <li class="gnb-li">
                <a href="{{ Cache::get('menuList')[$i]['link'] }}">{{ Cache::get('menuList')[$i]['name'] }}</a>
        @endif
            </li>
        @endfor

            <li class="gnb-li"><a href="{{ route('new.index') }}">새글</a></li>
            @if (Auth::guest()) <!-- 공개권한: 게스트 -->
            <li class="gnb-li"><a href="{{ route('login'). '?nextUrl='. Request::getRequestUri() }}">로그인</a></li>
            <li class="gnb-li"><a href="{{ route('user.join') }}">회원가입</a></li>
            @else <!-- else -->
                @php
                    $isAdmin = auth()->user()->isAdmin();
                @endphp
                @if($isAdmin) <!-- 공개권한: 관리자 -->
                    <li class="gnb-li"><a href="{{ route('admin.index') }}">관리자 모드</a></li>
                @endif <!-- 공개권한: 관리자 end -->
                <!-- 공개권한: 회원 -->
                <li class="gnb-li dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        {{ Auth::user()->nick }} <span class="caret"></span>
                    </a>
                    <!-- 2depth -->
                    <ul class="dropdown-menu" role="menu">

                        @if(!Auth::user()->isSuperAdmin())
                            <li><a href="{{ route('user.checkPassword') }}?work=edit">회원 정보 수정</a></li>
                        @endif
                        <li>
                            <a href="{{ route('scrap.index') }}" class="winScrap" target="_blank" onclick="winScrap(this.href); return false;">스크랩</a>
                        </li>
                        <li><a href="{{ route('user.point', Auth::user()->id) }}" class="point">포인트 내역</a></li>
                        <li><a href="{{ route('memo.index') }}?kind=recv" class="winMemo" target="_blank" onclick="winMemo(this.href); return false;">쪽지 <span class="memocount">{{ App\Memo::where('recv_user_id', Auth::user()->id)->where('read_timestamp', null)->count() }}</span></a></li>
                        <li>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                로그 아웃
                            </a>
                            <!-- 로그아웃 토큰 -->
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                        @if(!$isAdmin)
                            <li><a href="{{ route('user.checkPassword') }}?work=leave">회원 탈퇴</a></li>
                        @endif
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
    @component("popular.default.list")
    @endcomponent

    {{-- @component("visit.default.list")
    @endcomponent --}}
    {{-- <section id="visit">
        <div class="container">
            <h2>접속자집계</h2>
            <dl>
                <dt>오늘</dt>
                <dd>2</dd>
                <dt>어제</dt>
                <dd>3</dd>
                <dt>최대</dt>
                <dd>3</dd>
                <dt>전체</dt>
                <dd>28</dd>
            </dl>
                </div>
    </section> --}}
    <div id="ft_copy">
        <div class="container">
            <div class="link">
                @foreach(App\Content::all() as $content)
                    <a href="{{ route('contents.show', $content->content_id) }}">{{ $content->subject }}</a>
                @endforeach
            </div>
            <div class="copy">
            대표 홍길동 | 전화 000-0000-0000 | 사업자 000-00-00000<br>
            Copyright © <b>소유하신 도메인.</b> All rights reserved.<br>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap core JavaScript -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="{{ asset('bootstrap/js/ie10-viewport-bug-workaround.js') }}"></script>
</body>
</html>
