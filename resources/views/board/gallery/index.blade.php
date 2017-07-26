@extends( 'layout.'. ($board->layout ? : cache('config.skin')->layout. '.basic') )

@section('title')
    {{ $board->subject }} 리스트 | {{ Cache::get("config.homepage")->title }}
@endsection

@section('include_script')
    <script src="{{ asset('js/common.js') }}"></script>
@endsection

@section('content')
    <div id="board" class="container">
        @if($board->content_head)
            {!! $board->content_head !!}
        @endif

        {{-- 뷰 페이지에서도 전체 목록 보이기 설정에 따라 목록을 보여줄 수 있기 때문에 content부분만 따로 페이지로 만들어서 포함시켰다 --}}
        @include('board.gallery.list')

        @if($board->content_tail)
            {!! $board->content_tail !!}
        @endif
    </div>
@endsection
