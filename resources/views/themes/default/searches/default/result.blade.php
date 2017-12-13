@extends("themes.". cache('config.theme')->name. ".layouts.basic")

@section('title')전체검색:{{ $keyword }} | {{ cache("config.homepage")->title }}@endsection

@section('include_css')
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/common.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/search.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div id="board" class="container">
            <div class="bd_header">
                <div class="bd_head">
                    <span>전체검색 결과</span>
                </div>
            </div>

            <form class="bd_sch">
                <ul>
                    <li class="sch_slt">
                        <label for="groupId" class="sr-only">게시판 그룹선택</label>
                        <select name="groupId" id="groupId">
                            <option value>전체분류</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" @if($groupId == $group->id) selected @endif>{{ $group->subject }}</option>
                            @endforeach
                        </select>
                    </li>
                    <li class="sch_slt">
                        <label for="kind" class="sr-only">검색조건</label>
                        <select name="kind" id="kind">
                            <option value="subject||content" @if($kind == 'subject||content') selected @endif>제목+내용</option>
                            <option value="subject" @if($kind == 'subject') selected @endif>제목</option>
                                <option value="content" @if($kind == 'content') selected @endif>내용</option>
                            <option value="email" @if($kind == 'email') selected @endif>회원이메일</option>
                            <option value="name" @if($kind == 'name') selected @endif>이름</option>
                        </select>
                    </li>
                    <li class="sch_kw">
                        <label for="keyword" class="sr-only">검색어</label>
                        <input type="text" name="keyword" value="{{ $keyword }}" id="keyword" class="search" required>
                        <button type="submit" class="search-icon">
                            <i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">검색</span>
                        </button>
                    </li>
                    <li class="sch_chk">
                        <input type="radio" name="operator" id="or" value="or" @if($operator == 'or') checked @endif><label for="or">OR</label>
                        <input type="radio" name="operator" id="and" value="and" @if($operator == 'and') checked @endif><label for="and">AND</label>
                    </li>
                </ul>
            </form>

            <div id="sch_result">
                <section id="sch_res_ov">
                    <h2>[{{ $keyword }}] 전체검색 결과 게시판</h2>
                    <dl>
                        <dt>게시판</dt>
                        <dd><strong class="sch_word">{{ notNullCount($boards) }}개</strong></dd>
                        <dt>게시물</dt>
                        <dd><strong class="sch_word">{{ $writes->total() }}개</strong></dd>
                    </dl>
                    <p>{{ $page }}/{{ $writes->lastPage() }} 페이지 열람 중</p>
                </section>

                <div class="sch_res_ctg">
                    <ul>
                        <li><a href="/searches?{{ $allBoardTabQueryString }}">전체게시판</a></li>
                        @foreach($boards as $board)
                            <li><a href="/searches?{{ $boardTabQueryString }}&amp;boardName={{ $board->boardName }}"><strong>{{ $board->boardSubject }}</strong> <span class="count">{{ notNullCount($boards) }}</span></a></li>
                        @endforeach
                    </ul>
                </div>

            @forelse($writes as $write)
                @if ($write->boardChange || $loop->first)
                    {{-- 페이징된 객체의 첫번째 모델이거나 다른 게시판으로 넘어갔을 때 --}}
                    @unless($loop->first)  {{-- 첫번째 모델이 아닐때 닫는 태그 추가한다. --}}
                                </ul>
                            </div>
                        </section>
                    @endunless
                <section id="sch_res_list">
                    <div class="sch_res_list_hd">
                        <span class="bdname"><a href="{{ route('board.index', $write->boardName). '?'. $commonQueryString }}">[{{ $write->boardSubject }}] 게시판 내 결과</a></span>
                        <span class="more">
                            <a href="{{ route('board.index', $write->boardName). '?'. $commonQueryString }}"><strong>[{{ $write->boardSubject }}]</strong> 결과 더보기<i class="fa fa-caret-right"></i></a>
                        </span>
                    </div>
                    <div class="sch_res_list_bd">
                        <ul>
                @endif
                            <li class="contents">
                                <span class="sch_subject">
                                    <a href="/bbs/{{ $write->boardName }}/views/{{ $write->parent. $write->queryString }}">{!! clean($write->subject) !!}</a>
                                    <a href="/bbs/{{ $write->boardName }}/views/{{ $write->parent. $write->queryString }}" target="_blank" style="margin-left:7px;">[새창으로 열기]</a>
                                </span>
                                <p>{!! clean($write->content) !!}</p>
                                <span class="sv_wrap">
                                @if($write->user_id)
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">{{ $write->name }}</a>
                                    @auth
                                    @component(getFrontSideview(), ['sideview' => 'other', 'id' => $write->user_id_hashKey, 'name' => $write->name, 'email' => $write->email])
                                    @endcomponent
                                    @else
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="{{ route('new.index') }}?nick={{ $write->name }}">전체게시물</a></li>
                                    </ul>
                                    @endauth
                                @else
                                    {{ $write->name }}
                                @endif
                                </span>
                                <span class="sch_datetime">{{ $write->created_at }}</span>
                            </li>
                @if($loop->last)    {{-- 마지막 모델일때 닫는 태그 추가한다. --}}
                        </ul>
                    </div>
                </section>
                @endif
            @empty
                <section id="sch_res_list">
                    <div class="sch_res_list_bd">
                        <span class="empty_table">
                            <i class="fa fa-exclamation-triangle"></i> 검색된 자료가 없습니다.
                        </span>
                    </div>
                </section>
            @endforelse
            </div>

            {{-- 페이지 처리 --}}
            {{ $writes->appends(Request::except('page'))->withPath('searches')->links() }}
        </div>
    </div>
</div>
@endsection
