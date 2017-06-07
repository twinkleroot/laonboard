@extends('themes.default.basic')

@section('title')
    메인 | {{ Cache::get("config.homepage")->title }} 
@endsection

@section('content')
<div class="container">
<div class="row">
    메인
</div>
</div>
@endsection
