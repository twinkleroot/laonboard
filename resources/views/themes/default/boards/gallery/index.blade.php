@extends("themes.". cache('config.theme')->name. ".layouts.". ($board->layout ? : 'basic'))

@section('title'){{ $board->subject }}게시판 | {{ cache("config.homepage")->title }}@stop

@section('include_css')
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/common.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/board.css') }}">
@stop

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
@stop

@section('content')
<div id="board" class="container">
@if($board->content_head)
    {!! $board->content_head !!}
@endif

@if(view()->exists("themes.default.boards.$skin.list"))
    @include("themes.default.boards.$skin.list")
@else
    @include("themes.default.boards.default.list")
@endif

@if($board->content_tail)
    {!! $board->content_tail !!}
@endif
</div>

@stop
