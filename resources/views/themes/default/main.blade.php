@extends('themes.default.basic')

@section('title')
    메인 | {{ Cache::get("config.homepage")->title }}
@endsection

@section('content')

{{-- 최근 게시물 리스트--}}
@include('themes.'. $latestSkin. '.latest')

@endsection
