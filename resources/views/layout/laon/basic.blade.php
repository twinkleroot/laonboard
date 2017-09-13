<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @yield('fisrt_include_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/laon/css/style.css') }}">
    @yield('include_css')
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('js/common.js') }}"></script>
    @yield('include_script')
</head>
<body>
@yield('popup')
<div id="header">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div class="sitetitle">
                <span class="img"><a href="{{ url('/') }}">
                    <img src="{{ asset('themes/laon/images/title_logo.gif') }}"></a>
                </span>
            </div>
        </div>
        <div id="navbar" class="navbar-collapse collapse headerMenu" aria-expanded="false" style="height: 1px;">
            <ul class="nav navbar-nav">
                <li class="headerSearch">
                    <form name="searchBox" method="get" action="{{ route('search')}}" onsubmit="">
                        <input type="hidden" name="kind" value="subject||content">
                        <fieldset>
                            <legend>사이트 내 전체검색</legend>
                            <label for="keyword" class="sr-only"><strong>검색어 필수</strong></label>
                            <input type="text" name="keyword" id="keyword" maxlength="20" placeholder="통합검색" required>
                            <button type="submit" class="btnSearch" id="searchSubmit" name="search">검색</button>
                        </fieldset>
                        <input type="hidden" name="operator" value="and">
                    </form>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                @for($i=0; $i<count(Cache::get('menuList')); $i++)
                @if(count(Cache::get('subMenuList')[$i]) > 0)
                    <li class="dropdown">
                        <a @if(Cache::get('menuList')[$i]['link']) href="{{ Cache::get('menuList')[$i]['link'] }}" @endif class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" target="_{{ Cache::get('menuList')[$i]['target'] }}">
                            {{ Cache::get('menuList')[$i]['name'] }}<span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                        @for($j=0; $j<count(Cache::get('subMenuList')[$i]); $j++)
                            <li>
                                <a href="{{ Cache::get('subMenuList')[$i][$j]['link'] }}" target="_{{ Cache::get('menuList')[$i]['target'] }}">
                                    {{Cache::get('subMenuList')[$i][$j]['name'] }}
                                </a>
                            </li>
                        @endfor
                        </ul>
                @else
                    <li>
                        <a href="{{ Cache::get('menuList')[$i]['link'] }}" target="_{{ Cache::get('menuList')[$i]['target'] }}">
                            {{ Cache::get('menuList')[$i]['name'] }}
                        </a>
                @endif
                    </li>
                @endfor
                <li><a href="{{ route('new.index') }}">새글</a></li>
                @if (Auth::guest())
                    <li><a href="{{ route('login'). '?nextUrl='. Request::getRequestUri() }}">로그인</a></li>
                    <li><a href="{{ route('user.join') }}">회원가입</a></li>
                @else
                    @php
                        $isAdmin = auth()->user()->isAdmin();
                    @endphp
                    @if($isAdmin)
                        <li><a href="{{ route('admin.index') }}">관리자 모드</a></li>
                    @endif
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            {{ Auth::user()->nick }}<span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            @if(!Auth::user()->isSuperAdmin())
                                <li><a href="{{ route('user.checkPassword') }}?work=edit">회원 정보 수정</a></li>
                            @endif
                                <li><a href="{{ route('scrap.index') }}" class="winScrap" target="_blank" onclick="winScrap(this.href); return false;">스크랩</a></li>
                            @if(cache('config.homepage')->usePoint)
                                <li><a href="{{ route('user.point', Auth::user()->id) }}" class="point">포인트 내역</a></li>
                            @endif
                            <li><a href="{{ route('memo.index') }}?kind=recv" class="winMemo" target="_blank" onclick="winMemo(this.href); return false;">쪽지 <span class="memocount">{{ App\Memo::where('recv_user_id', Auth::user()->id)->where('read_timestamp', null)->count() }}</span></a></li>
                            <li>
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    로그아웃
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                            @if(!$isAdmin)
                                <li><a href="{{ route('user.checkPassword') }}?work=leave">회원 탈퇴</a></li>
                            @endif
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
<div id="contents">
    @yield('content')
</div>
<footer id="footer">
    @component("popular.default.list")
    @endcomponent
    <div id="ft_copy">
        <div class="container">
            <div class="link">
                @foreach(App\Content::all() as $content)
                    <a href="{{ route('content.show', $content->content_id) }}">{{ $content->subject }}</a>
                @endforeach
            </div>
            <div class="copy">
                Copyright © <strong>laonboard.com</strong>. All rights reserved.
            </div>
        </div>
    </div>
</footer>
<script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('bootstrap/js/ie10-viewport-bug-workaround.js') }}"></script>
</body>
</html>
