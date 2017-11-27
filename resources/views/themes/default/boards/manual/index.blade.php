@extends("themes.". cache('config.theme')->name. ".layouts.". ($board->layout ? : 'basic'))

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
                @if($writes->first())
                <li class="next">
                    <a href="/bbs/{{ $board->table_name }}/views/{{ $writes->first()->parent }}">다음으로</a>
                </li>
                @else
                <li class="next rock">
                    <a>다음 게시물이 없습니다</a>
                </li>
                @endif
            </ul>
        </div>
    </div>
</div>
@stop
