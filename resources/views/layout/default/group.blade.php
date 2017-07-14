@extends('layout.'. cache('config.skin')->layout. '.basic')

@section('title')
    {{ $groupName }} | {{ Cache::get("config.homepage")->title }}
@endsection

@section('content')
    {{-- 최근 게시물 리스트 --}}
    @include("latest.$skin.index")
@endsection
