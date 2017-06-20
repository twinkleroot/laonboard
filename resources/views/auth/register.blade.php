@extends('themes.'. $skin. '.basic')

@section('title')
    회원가입 | {{ Cache::get("config.homepage")->title }}
@endsection

@section('include_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/'. $skin. '/css/auth.css') }}">
@endsection

@section('include_script')
    <script src='https://www.google.com/recaptcha/api.js'></script>
@endsection

@section('content')

    @include('themes.'. $skin. '.auth.register')

@endsection
