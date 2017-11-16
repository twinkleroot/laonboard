@extends("themes.$theme.layouts.basic")

@section('title'){{ $theme }} 테마 미리보기 | {{ cache("config.homepage")->title }}@endsection

@section('include_css')
@if($type == 'index')
<link rel="stylesheet" type="text/css" href="{{ File::exists(public_path("themes/$theme/css/latest.css")) ?  ver_asset("themes/$theme/css/latest.css") : ver_asset("themes/default/css/latest.css")}}">
@else
<link rel="stylesheet" type="text/css" href="{{ File::exists(public_path("themes/$theme/css/common.css")) ?  ver_asset("themes/$theme/css/common.css") : ver_asset('themes/default/css/common.css') }}">
<link rel="stylesheet" type="text/css" href="{{ File::exists(public_path("themes/$theme/css/board.css")) ?  ver_asset("themes/$theme/css/board.css") : ver_asset("themes/default/css/board.css") }}">
<link rel="stylesheet" type="text/css" href="{{ File::exists(public_path("themes/$theme/css/common.css")) ?  ver_asset("themes/$theme/css/common.css") : ver_asset("themes/default/css/common.css") }}">
@endif
@endsection

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
@if($type != 'index')
<script src="{{ ver_asset('js/viewimageresize.js') }}"></script>
@endif
@endsection

@section('content')
<style>
body {
    margin-top: 54px;
}
#preview_item {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background: #333;
    width: 100%;
    text-align: center;
    display: block;
    height: 54px;
}
#preview_item ul {
    padding: 10px 0;
    margin: 0 auto;
    display: inline-block;
}
#preview_item ul:after {
    content: '';
    display: block;
    clear: both;
}
#preview_item li {
    float: left;
    list-style: none;
    margin: 0 5px;
}
</style>
<section id="preview_item">
    <ul>
        <li><a href="{{ route('admin.themes.preview.index', $theme) }}" class="btn btn-default" role="button">인덱스 화면</a></li>
        <li><a href="{{ route('admin.themes.preview.board.list', $theme) }}" class="btn btn-default" role="button">게시글 리스트</a></li>
        <li><a href="{{ route('admin.themes.preview.board.view', $theme) }}" class="btn btn-default" role="button">게시글 보기</a></li>
    </ul>
</section>

<section id="preview_content">
    @if($type == 'index')
        {{ fireEvent('mainContents') }}
    @elseif($type == 'boardList')
    <div id="board" class="container">
        @if($board->content_head)
        {!! $board->content_head !!}
        @endif

        @if(view()->exists("themes.$theme.boards.$skin.indexContent"))
            @include("themes.$theme.boards.$skin.indexContent")
        @else
            @include("themes.default.boards.default.indexContent")
        @endif

        @if($board->content_tail)
        {!! $board->content_tail !!}
        @endif
    </div>
    @else
    <div id="board" class="container">
        @if($board->content_head)
        {!! $board->content_head !!}
        @endif

        {{-- 테마설정의 미리보기 때문에 글 보기 내용 부분은 따로 분리했다. --}}
        @if(view()->exists("themes.$theme.boards.$skin.viewContent"))
            @include("themes.$theme.boards.$skin.viewContent")
        @else
            @include("themes.default.boards.default.viewContent")
        @endif

        @if($board->content_tail)
        {!! $board->content_tail !!}
        @endif
    </div>
    @endif
</section>
@endsection
