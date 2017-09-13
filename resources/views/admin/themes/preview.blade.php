@extends("layout.$theme.basic")

@section('title')
    {{ $info['themeName'] }} 테마 미리보기 | {{ cache("config.homepage")->title }}
@endsection

@section('include_css')
    @if($type == 'index')
    <link rel="stylesheet" type="text/css" href="{{ File::exists(public_path("themes/$theme/css/latest.css")) ?  asset("themes/$theme/css/latest.css") : asset("themes/default/css/latest.css")}}">
    @else
    <link rel="stylesheet" type="text/css" href="{{ File::exists(public_path("themes/$theme/css/board.css")) ?  asset("themes/$theme/css/board.css") : asset("themes/default/css/board.css") }}">
    @endif
@endsection

@section('include_script')
    <script src="{{ asset('js/common.js') }}"></script>
    @if($type != 'index')
    <script src="{{ asset('js/viewimageresize.js') }}"></script>
    <script src="https://www.google.com/recaptcha/api.js"></script>
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
</section>
@endsection
