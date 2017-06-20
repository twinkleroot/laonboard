@extends('themes.'. $skin. '.basic')

@section('title')
    소셜 로그인 | {{ Cache::get("config.homepage")->title }}
@endsection

@section('include_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/'. $skin. '/css/auth.css') }}">
@endsection

@section('include_script')
<script>
function joinValidation(form) {
    if(form.password.value.length < 3) {
        alert('비밀번호를 3글자 이상 입력하십시오.');
        form.password.focus();
        return false;
    }

    if(form.password_confirmation.value.length < 3) {
        alert('비밀번호 확인을 3글자 이상 입력하십시오.');
        form.password_confirmation.focus();
        return false;
    }

    if(form.password.value != form.password_confirmation.value) {
        alert('비밀번호가 같지 않습니다.');
        form.password_confirmation.focus();
        return false;
    }

    if(form.nick.value == '') {
        alert('닉네임을 입력해 주세요.');
        form.nick.focus();
        return false;
    }

    if(form.nick.value == '{{ $userFromSocial->nickname }}') {
        alert('다른 닉네임을 입력해 주세요.');
        form.nick.focus();
        return false;
    }

    if(form.email.value == '') {
        alert('이메일을 입력해 주세요.');
        form.email.focus();
        return false;
    }

    if(form.email.value == '{{ $userFromSocial->email }}') {
        alert('다른 이메일을 입력해 주세요.');
        form.email.focus();
        return false;
    }

    return true;
}

function loginValidation(form) {
    if(form.email.value == '') {
        alert('이메일 : 필수 입력입니다.');
        form.email.focus();
        return false;
    }

    if(form.password.value == '') {
        alert('비밀번호 : 필수 입력입니다.');
        form.password.focus();
        return false;
    }

    return true;
}
</script>
@endsection
@section('content')

    @include('themes.'. $skin. '.auth.social')

@endsection
