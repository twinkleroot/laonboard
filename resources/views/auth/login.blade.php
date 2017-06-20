@extends('themes.default.basic')

@section('title')
    로그인 | {{ Cache::get("config.homepage")->title }}
@endsection

@section('include_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/auth.css') }}">
@endsection

@section('content')

@include('themes.default.auth.login')

@endsection
