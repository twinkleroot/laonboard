@extends('install.layout')

@section('title')오류! {{ config('app.name') }} 설치하기@endsection

@section('step')
    Message
@endsection

@section('content')
<div class="container">
    <h1 class="install_uncnf">{{ config('app.name') }}를 먼저 설치해주십시오.</h1>
    <div class="ins_inner">
        <p>설치 정보를 찾을 수 없습니다.<br>
        {{ config('app.name') }} 설치 후 다시 실행하시기 바랍니다.</p>
        <div class="inner_btn">
            <a href="{{ route('install.license') }}" class="btn">{{ config('app.name') }} 설치하기</a>
        </div>
    </div>
</div>
@endsection
