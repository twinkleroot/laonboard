@extends('install.layout')

@section('title')
    오류! {{ config('app.name') }} 설치하기
@endsection

@section('step')
    Message
@endsection

@section('content')
<h1>{{ config('app.name') }}를 먼저 설치해주십시오.</h1>
<div class="ins_inner">
    <p>다음 파일을 찾을 수 없습니다.</p>
    <ul>
        <li><strong>/.env</strong></li>
    </ul>
    <p>{{ config('app.name') }} 설치 후 다시 실행하시기 바랍니다.</p>
    <div class="inner_btn">
        <a href="{{ route('install.license') }}">{{ config('app.name') }} 설치하기</a>
    </div>
</div>
@endsection