@extends("themes.default.layouts.basic")

@section('title'){{ cache("config.homepage")->title }}@endsection

@section('include_css')
<!-- common -->
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/common.css') }}">
<!-- 최근게시물용 CSS파일 -->
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/latest.css') }}">
<!-- 팝업레이어용 CSS파일 -->
<link rel="stylesheet" type="text/css" href="{{ ver_asset('css/popuplayer.css') }}">
@endsection

{{-- 팝업 레이어 --}}
@section('popup')
    @include('themes.default.popups.index')
@endsection

@section('content')
    {{ fireEvent('mainContents') }}
@endsection
