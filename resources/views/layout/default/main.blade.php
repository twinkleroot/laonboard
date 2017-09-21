@extends('layout.'. cache('config.skin')->layout. '.basic')

@section('title')메인 | {{ cache("config.homepage")->title }}@endsection

@section('include_css')
<!-- common -->
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/'.$skin.'/css/common.css') }}">
<!-- 최근게시물용 CSS파일 -->
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/'.$skin.'/css/latest.css') }}">
<!-- 팝업레이어용 CSS파일 -->
<link rel="stylesheet" type="text/css" href="{{ ver_asset('css/popuplayer.css') }}">
@endsection

{{-- 팝업 레이어 --}}
@section('popup')
    @include('board.popup')
@endsection

@section('content')
    {{-- 최근 게시물 리스트--}}
    @include("latest.$skin.index")
@endsection
