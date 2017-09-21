@extends('install.layout')

@section('title'){{  config('app.name')." 설치 완료 3/3" }}@endsection

@section('step')
    INSTALLATION
@endsection

@section('content')
<div class="container">
    <ul class="step">
        <li>1. 라이센스 확인</li>
        <li>2. 초기환경설정</li>
        <li class="on">3. 설치 완료</li>
    </ul>
    @if(isset($dbError) && $dbError)
    <div class="ins_inner">
        <p>MySQL Host, Port, DB, User, Password 를 확인해 주십시오.</p>
        @if (isset($message))
            <p>{{ $message }}</p>
        @endif
        <div class="inner_btn"><a onclick="history.back();" class="btn">뒤로가기</a></div>
    </div>
    @else
    <div class="ins_inner">
        <p>축하합니다. {{ config('app.name') }} 설치가 완료되었습니다.</p>
        <h2>환경설정 변경은 다음의 과정을 따르십시오.</h2>
        <ol>
            <li>메인화면으로 이동</li>
            <li>관리자 로그인</li>
            <li>관리자 모드 접속</li>
            <li>환경설정 메뉴의 기본환경설정 페이지로 이동</li>
        </ol>
        <div class="inner_btn">
            <a href="{{ route('home') }}" class="btn">새로운 {{ config('app.name') }}로 이동</a>
        </div>
    </div>
    @endif
</div>
<script>
function frm_submit(f)
{
    if (!f.agree.checked) {
        alert("라이센스 내용에 동의하셔야 설치가 가능합니다.");
        return false;
    }
    return true;
}
</script>
@endsection
