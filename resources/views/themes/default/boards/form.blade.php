@extends("themes.default.layouts.". ($board->layout ? : 'basic'))

@section('title'){{ $board->subject }}게시판 | {{ cache("config.homepage")->title }}@endsection

@section('include_css')
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/common.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/board.css') }}">
@endsection

@section('include_script')
@if(isMobile())
<script src="https://cloud.tinymce.com/dev/tinymce.min.js"></script>
@else
<script src="{{ ver_asset('tinymce/tinymce.min.js') }}"></script>
@endif
@endsection

@section('content')
<div id="board" class="container">
    @if($board->content_head)
        {!! $board->content_head !!}
    @endif

    @if(view()->exists("themes.default.boards.$skin.formContent"))
        @include("themes.default.boards.$skin.formContent")
    @else
        @include("themes.default.boards.default.formContent")
    @endif

    @if($board->content_tail)
        {!! $board->content_tail !!}
    @endif
</div>
@endsection
