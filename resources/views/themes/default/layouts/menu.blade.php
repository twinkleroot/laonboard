<ul class="gnb navbar-nav navbar-right">
@for($i=0; $i<notNullCount(cache('menuList')); $i++)
@if(notNullCount(cache('subMenuList')[$i]) > 0)
    <li class="gnb-li dropdown">
        <a @if(cache('menuList')[$i]['link'])href="{{ cache('menuList')[$i]['link'] }}"@else href="#"@endif role="button" aria-expanded="false" target="_{{ cache('menuList')[$i]['target'] }}">
            {{ cache('menuList')[$i]['name'] }}<span class="caret"></span>
        </a>
        <ul class="dropdown-menu" role="menu">
        @for($j=0; $j<notNullCount(cache('subMenuList')[$i]); $j++)
            <li>
                <a href="{{ cache('subMenuList')[$i][$j]['link'] }}" target="_{{ cache('menuList')[$i]['target'] }}">{{cache('subMenuList')[$i][$j]['name'] }}</a>
            </li>
        @endfor
        </ul>
@else
    <li class="gnb-li">
        <a href="{{ cache('menuList')[$i]['link'] }}" target="_{{ cache('menuList')[$i]['target'] }}">{{ cache('menuList')[$i]['name'] }}</a>
@endif
    </li>
@endfor
    <li class="gnb-li"><a href="{{ route('new.index') }}">새글</a></li>
    @unless(auth()->check())
        <li class="gnb-li"><a href="{{ route('login'). '?nextUrl='. Request::getRequestUri() }}">로그인</a></li>
        <li class="gnb-li"><a href="{{ route('user.join') }}">회원가입</a></li>
    @else
        @if(session()->get('admin'))
            <li class="gnb-li"><a href="{{ route('admin.index') }}">관리자 모드</a></li>
        @endif
        <li class="gnb-li dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                {{ Auth::user()->nick }} <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" role="menu">
                @unless(Auth::user()->isSuperAdmin())
                    <li><a href="{{ route('user.checkPassword') }}?work=edit">회원 정보 수정</a></li>
                @endunless
                <li>
                    <a href="{{ route('scrap.index') }}" class="winScrap" target="_blank" onclick="winScrap(this.href); return false;">스크랩</a>
                </li>
                @if(cache('config.homepage')->usePoint)
                <li><a href="{{ route('user.point', Auth::user()->id_hashkey) }}" class="point">포인트 내역</a></li>
                @endif
                <li><a href="{{ route('memo.index') }}?kind=recv" class="winMemo" target="_blank" onclick="winMemo(this.href); return false;">쪽지 <span class="memocount">{{ App\Models\Memo::where('recv_user_id', Auth::user()->id)->where('read_timestamp', null)->count() }}</span></a></li>
                {{ fireEvent('menuContents') }}
                <li>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        로그 아웃
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </li>
                @unless(session()->get('admin'))
                    <li><a href="{{ route('user.checkPassword') }}?work=leave">회원 탈퇴</a></li>
                @endunless
            </ul>
        </li>
    @endunless
</ul>
