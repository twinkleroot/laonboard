@extends("themes.default.layouts.". ($board->layout ? : 'basic'))

@section('title'){{ $write->subject }} > {{ $board->subject }} | {{ Cache::get('config.homepage')->title }}@stop

@section('include_script')
<script src="{{ ver_asset('js/viewimageresize.js') }}"></script>
<script src="{{ ver_asset('js/common.js') }}"></script>
@stop

@section('include_css')
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/common.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/manual.css') }}">
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
            @php
                $user = isset($user) ? $user : auth()->user();
            @endphp
            <div id="postadm">
            @if( (auth()->check() && $user->id_hashkey == $write->user_id) || !$write->user_id || session()->get('admin') )
                <ul class="postadm">
                    <li>
                        <a href="/bbs/{{ $board->table_name }}/edit/{{ $write->id. (Request::getQueryString() ? '?'.Request::getQueryString() : '') }}" class="btn btn-default">
                            수정
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('board.destroy', ['boardName' => $board->table_name, 'writeId' => $write->id]). (Request::getQueryString() ? '?'.Request::getQueryString() : '') }}" onclick="del(this.href); return false;" class="btn btn-default">
                            삭제
                        </a>
                    </li>
                    <li>
                        <a class="movePopup btn btn-default" href="{{ route('board.view.move', $board->table_name)}}?type=copy&amp;writeId={{ $write->id }}" target="move">
                            복사
                        </a>
                    </li>
                    <li>
                        <a class="movePopup btn btn-default" href="{{ route('board.view.move', $board->table_name)}}?type=move&amp;writeId={{ $write->id }}" target="move">
                            이동
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('board.create.reply', ['board' => $board->table_name, 'writeId' => $write->id]). (Request::getQueryString() ? '?'.Request::getQueryString() : '') }}" class="btn btn-default">
                            답변
                        </a>
                    </li>
                </ul>
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
                <div class="text">
                    <span class="title">{{ $write->subject }}</span>
                </div>
            </div>
            <div id="main_body">
                {!! $write->content !!}
            </div>
            <ul id="manual_bottom">
                <li class="before @if($prevUrl != '')">
                    <a href="{{ $prevUrl }}">{{ $prevSubject }}
                    @else
                         rock">
                        <a>이전 게시물이 없습니다</a>
                    @endif
                    </a>
                </li>
                <li class="next @if($nextUrl != '')">
                    <a href="{{ $nextUrl }}">{{ $nextSubject }}
                    @else
                         rock">
                        <a>다음 게시물이 없습니다</a>
                    @endif
                    </a>
                </li>
            </ul>
            {{-- 댓글 --}}
            @if(view()->exists('themes.'. cache('config.theme')->name. ".boards.$skin.comment"))
                @include('themes.'. cache('config.theme')->name. ".boards.$skin.comment")
            @else
                @include('themes.'. cache('config.theme')->name. ".boards.default.comment")
            @endif
        </div>
    </div>
</div>
@stop
