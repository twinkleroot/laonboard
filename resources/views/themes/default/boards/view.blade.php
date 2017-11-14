@extends("themes.default.layouts.". ($board->layout ? : 'basic'))

@section('title'){{ $write->subject }} > {{ $board->subject }} | {{ cache('config.homepage')->title }}@endsection

@section('include_script')
<script src="{{ ver_asset('js/viewimageresize.js') }}"></script>
<script src="{{ ver_asset('js/common.js') }}"></script>
@endsection

@section('include_css')
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/common.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/board.css') }}">
@endsection

@section('content')
<!-- Board start -->
<div id="board" class="container">
@if($board->content_head)
    {!! $board->content_head !!}
@endif

    {{-- 테마설정의 미리보기 때문에 글 보기 내용 부분은 따로 분리했다. --}}
    @if(view()->exists("themes.default.boards.$skin.viewContent"))
        @include("themes.default.boards.$skin.viewContent")
    @else
        @include("themes.default.boards.default.viewContent")
    @endif

    {{-- 전체 목록 보이기 설정시 --}}
@if($board->use_list_view)
    @if(view()->exists("themes.default.boards.$skin.indexContent"))
        @include("themes.default.boards.$skin.indexContent")
    @else
        @include("themes.default.boards.default.indexContent")
    @endif
@endif

@if($board->content_tail)
    {!! $board->content_tail !!}
@endif
</div>
@endsection
