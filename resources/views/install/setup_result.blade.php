@extends('install.layout')

@section('title')
    {{  config('app.name')." 설치 완료 3/3" }}
@endsection

@section('step')
    INSTALLATION
@endsection

@section('content')

@if(isset($dbError) && $dbError)
<div class="ins_inner">
    <p>MySQL Host, Port, DB, User, Password 를 확인해 주십시오.</p>
    @if (isset($message))
    <p>{{ $message }}</p>
    @endif
    <div class="inner_btn"><a href="{{ route('install.form') }}">뒤로가기</a></div>
</div>
@else
<div class="ins_inner">

    <p>축하합니다. {{ config('app.name') }} 설치가 완료되었습니다.</p>

</div>

<div class="ins_inner">

    <h2>환경설정 변경은 다음의 과정을 따르십시오.</h2>

    <ol>
        <li>메인화면으로 이동</li>
        <li>관리자 로그인</li>
        <li>관리자 모드 접속</li>
        <li>환경설정 메뉴의 기본환경설정 페이지로 이동</li>
    </ol>

    <div class="inner_btn">
        <a href="{{ route('/') }}">새로운 {{ config('app.name') }}로 이동</a>
    </div>

</div>
@endif

@endsection
