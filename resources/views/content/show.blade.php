@extends('themes.default.basic')

@section('title')
    {{ $content->subject }} | {{ Cache::get("config.homepage")->title }}
@endsection

@section('content')

    @include('themes.'. ($content->skin ? : 'default'). '.content.show')

@endsection
