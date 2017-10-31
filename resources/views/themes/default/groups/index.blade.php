@extends("themes.default.layouts.basic")

@section('title'){{ $groupName }} | {{ Cache::get("config.homepage")->title }}@endsection

@section('include_css')
<!-- 최근게시물용 CSS파일 -->
<link rel="stylesheet" type="text/css" href="{{ ver_asset("themes/default/css/latest.css") }}">
@endsection

@section('content')
{{-- 최근 게시물 리스트 --}}
@include("themes.default.latests.$skin.index")
@endsection
