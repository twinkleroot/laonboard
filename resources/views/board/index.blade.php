@extends('theme')

@section('title')
    {{ $board->subject }} 리스트 | {{ App\Config::getConfig('config.homepage')->title }}
@endsection

@section('content')

    {{-- 뷰 페이지에서도 전체 목록 보이기 설정에 따라 목록을 보여줄 수 있기 때문에 content부분만 따로 페이지로 만들어서 포함시켰다 --}}
    @include('board.list')

@endsection
