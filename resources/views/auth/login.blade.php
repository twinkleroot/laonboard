@extends('themes.default.basic')

@section('title')
    로그인 | {{ Cache::get("config.homepage")->title }}
@endsection

@section('content')

@include('themes.default.auth.login')

@endsection
