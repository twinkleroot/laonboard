@extends( 'layout.'. ($board->layout ? : cache('config.skin')->layout. '.basic') )

@section('title')
    {{ $write->subject }} > {{ $board->subject }} | {{ Cache::get('config.homepage')->title }}
@endsection

@section('include_script')
    <script src="{{ asset('js/viewimageresize.js') }}"></script>
    <script src="{{ asset('js/common.js') }}"></script>
    <script src="https://www.google.com/recaptcha/api.js"></script>
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
        @include('board.gallery.list')
    @endif

    @if($board->content_tail)
        {!! $board->content_tail !!}
    @endif
</div>

@endsection
