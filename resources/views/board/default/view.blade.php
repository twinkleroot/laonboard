@extends( 'layout.'. ($board->layout ? : cache('config.skin')->layout. '.basic') )

@section('title')
    {{ $write->subject }} > {{ $board->subject }} | {{ Cache::get('config.homepage')->title }}
@endsection

@section('include_script')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
    @include('board.default.view_content')

    {{-- 전체 목록 보이기 설정시 --}}
    @if($board->use_list_view)
        @include('board.default.list')
    @endif

    @if($board->content_tail)
        {!! $board->content_tail !!}
    @endif
</div>

@endsection
