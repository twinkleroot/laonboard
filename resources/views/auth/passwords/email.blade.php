@extends('themes.'. $skin. '.basic')

@section('title')
    비밀번호 재설정 | {{ Cache::get("config.homepage")->title }}
@endsection

@section('content')

    @include('themes.'. $skin. '.auth.email')

@endsection
