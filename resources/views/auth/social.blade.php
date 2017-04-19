@extends('theme')

@section('title')
    소셜 로그인 완료 | LaBoard
@endsection

@section('include_script')
<script>
$(function(){
    // 계속하기 - step 1(회원가입 폼)
    $('#continue').click(function(){
        $('#loginForm').hide();
        $('#joinForm').show();
    });

    // 기존 계정과 연결 - step 1(계정, 비밀번호 비교 폼)
    $('#connect').click(function(){
        $('#joinForm').hide();
        $('#loginForm').show();
    });
});

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
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{ $userFromSocial->getNickname() }}님 환영합니다.</div>

                    <div class="panel-body">
                        <input type="button" id="continue" value="계속 하기" />
                        <input type="button" id="connect" value="기존 계정과 연결" />
                    </div>

                    <div class="panel-body" id="joinForm" style="display:none">
                    <form method="POST" action="{{ route('social.socialUserJoin') }}"
                        onsubmit="return joinValidation(this);" autocomplete="off">
                        {{ csrf_field() }}
                        <input type="hidden" name="provider" value="{{ $provider }}" />
                        <p>
                            <span class="help-block">
                                <strong>{{ $message['password'] }}</strong>
                            </span>
                            비밀번호 <input type="password" name="password" minlength="3" maxlength="20" required />
                        </p>
                        <p>
                            비밀번호 확인 <input type="password" name="password_confirmation"
                                            minlength="3" maxlength="20" required />
                        </p>
                        <p>
                            @if(array_has($message, 'nick'))
                            <span class="help-block">
                                <strong>{{ $message['nick'] }}</strong>
                            </span>
                            @endif
                            닉네임 <input type="text" name="nick" value="{{ $userFromSocial->nickname }}" required />
                        </p>
                        <p>
                            @if(array_has($message, 'email'))
                            <span class="help-block">
                                <strong>{{ $message['email'] }}</strong>
                            </span>
                            @endif
                            이메일 <input type="email" name="email" value="{{ $userFromSocial->email }}" required />
                        </p>
                        <input type="submit" id="userJoin" value="회원가입"/>
                    </form>
                    </div>

                    <div class="panel-body" id="loginForm" style="display:none">
                    <form method="POST" action="{{ route('social.connectExistAccount') }}"
                        onsubmit="return loginValidation(form);" autocomplete="off">
                        {{ csrf_field() }}
                        <p>
                            <input type="hidden" name="provider" value="{{ $provider }}" />
                            기존 이메일 <input type="text" name="email" maxlength="20" required />
                            비밀번호 <input type="password" name="password" maxlength="20" required />
                        </p>
                        <input type="submit" id="connectExistAccount" value="연결하고 로그인하기"/>
                    </form>
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection
