@extends('layouts.default.basic')

@section('title')
    메인 | {{ Cache::get("config.homepage")->title }}
@endsection

{{-- 팝업 레이어 --}}
@section('popup')
    @include('board.popup')
@endsection

@section('content')
    {{-- 최근 게시물 리스트--}}
    @include('latest.default.index')
@endsection
