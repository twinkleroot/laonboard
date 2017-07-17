@extends("layout.$theme.basic")

@section('title')
    {{ $info['themeName'] }} 테마 미리보기 | {{ cache("config.homepage")->title }}
@endsection

@section('content')
<div>
    <a href="{{ route('admin.themes.preview.index', $theme) }}">인덱스 화면</a>
    <a href="{{ route('admin.themes.preview.board.list', $theme) }}">게시글 리스트</a>
    <a href="{{ route('admin.themes.preview.board.view', $theme) }}">게시글 보기</a>
</div>

    @if($type == 'index')
        @include("latest.$skin.index")
    @elseif($type == 'boardList')
        <div id="board" class="container">
            @if($board->content_head)
                {!! $board->content_head !!}
            @endif

            @include("board.$themeName.list")

            @if($board->content_tail)
                {!! $board->content_tail !!}
            @endif
        </div>
    @else
        <div id="board" class="container">
            @if($board->content_head)
                {!! $board->content_head !!}
            @endif

            @include("board.$themeName.view_content")

            @if($board->content_tail)
                {!! $board->content_tail !!}
            @endif
        </div>
    @endif

@endsection
