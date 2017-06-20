@extends('themes.'. $skin. '.basic')

@section('title')
    로그인 | {{ Cache::get("config.homepage")->title }}
@endsection

@section('include_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/'. $skin. '/css/auth.css') }}">
@endsection

@section('content')

@include('themes.'. $skin. '.auth.login')

@endsection
