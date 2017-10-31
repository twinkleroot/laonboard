@extends("themes.default.layouts.". ($board->layout ? : 'basic'))

@section('title'){{ $board->subject }}게시판 | {{ cache("config.homepage")->title }}@endsection

@section('include_css')
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/common.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/board.css') }}">
@endsection

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
@endsection

@section('content')
<div id="board" class="container">
@if($board->content_head)
    {!! $board->content_head !!}
@endif

    @if(view()->exists("themes.default.boards.$skin.indexContent"))
        @include("themes.default.boards.$skin.indexContent")
    @else
        @include("themes.default.boards.default.indexContent")
    @endif

@if($board->content_tail)
    {!! $board->content_tail !!}
@endif
</div>
@endsection
