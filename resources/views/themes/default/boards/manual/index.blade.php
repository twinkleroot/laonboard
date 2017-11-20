@extends("themes.default.layouts.". ($board->layout ? : 'basic'))

@section('title'){{ $board->subject }}게시판 | {{ Cache::get("config.homepage")->title }}@stop

@section('include_css')
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/common.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/manual.css') }}">
@stop

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
@stop

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            @if($board->use_list_view)
                @include('themes.default.boards.manual.list')
            @endif
            <div id="sub_search">
                <form method="get" action="{{ route('board.index', $board->table_name) }}" onsubmit="return searchFormSubmit(this);">
                @if($currenctCategory != '')
                    <input type="hidden" id='category' name='category' value='{{ $currenctCategory }}' />
                @endif
                <label for="kind" class="sr-only">검색대상</label>
                    <select name="kind" id="kind" class="sr-only">
                        <option value="content" @if($kind == 'content') selected @endif>내용</option>
                    </select>
                    <label for="keyword" class="sr-only">검색어</label>
                    <input type="text" name="keyword" id="keyword" value="{{ $kind != 'user_id' ? $keyword : '' }}" class="search" required>
                    <button type="submit" class="search-icon">
                        <i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">검색</span>
                    </button>
                </form>
            </div>
        </div>
        <div class="col-md-9">
            <div id="postadm">
            @if(auth()->user() && auth()->user()->isBoardAdmin($board))
                <ul class="postadm right">
                    <li>
                        <a href="{{ route('admin.boards.edit', $board->table_name) }}" class="btn btn-danger">게시판설정</a>
                    </li>
                    <li>
                        <a href="{{ route('board.create', $board->table_name). '?'. $request->getQueryString() }}" class="btn btn-sir">글쓰기</a>
                    </li>
                </ul>
            @endif
            </div>
            <div id="main_top">
                @if($board->content_head)
                    {!! $board->content_head !!}
                @endif
            </div>
            <div id="main_body">
                @if($board->content_tail)
                    {!! $board->content_tail !!}
                @endif
            </div>
            <ul id="manual_bottom">
                <li class="before rock">
                    <a>이전 게시물이 없습니다</a>
                </li>
                <li class="next">
                    <a href="/bbs/{{ $board->table_name }}/views/{{ $writes->first()->parent }}">다음으로</a>
                </li>
            </ul>
        </div>
    </div>
</div>
@stop
