@extends('layout.'. cache('config.skin')->layout. '.basic')

@section('title')
    소셜 로그인 | {{ Cache::get("config.homepage")->title }}
@endsection

@section('include_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/auth.css') }}">
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

    if(checkExistData('nick', form.nick.value)) {
        alert('이미 가입된 닉네임입니다. 다른 닉네임을 입력해 주세요.');
        form.nick.focus();
        return false;
    }

    if(form.email.value == '') {
        alert('이메일을 입력해 주세요.');
        form.email.focus();
        return false;
    }

    if(checkExistData('email', form.email.value)) {
        alert('이미 가입된 이메일입니다. 다른 이메일을 입력해 주세요.');
        form.email.focus();
        return false;
    }

    return true;
}

function checkExistData(key, value) {
    var data = {
        'key' : key,
        'value' : value,
        '_token' : '{{ csrf_token() }}'
    };
    var result = false;
    $.ajax({
        url: '/user/existData',
        type: 'POST',
        data: data,
        dataType: 'json',
        async: false,
        cache: false,
        success: function(data) {
            result = data.result;
        }
    });

    return result;
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

    if(checkExistData('email', form.email.value) != true) {
        alert('가입되지 않은 이메일입니다. 확인 후 다시 입력해 주세요.');
        form.email.focus();
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
                <div class="panel-heading"><h3>{{ $userFromSocial->getNickname() }}님 환영합니다.</h3></div>

                    <div class="panel-body">
                        <h4>새로운 회원 가입</h4>
                    </div>

                    <div class="panel-body">
                    <form method="POST" action="{{ route('social.socialUserJoin') }}" onsubmit="return joinValidation(this);" autocomplete="off">
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

                    <div class="panel-body">
                        <hr />
                    </div>

                    <div class="panel-body">
                        <h4>기존 계정과 연결</h4>
                    </div>
                    <div class="panel-body">
                    <form method="POST" action="{{ route('social.connectExistAccount') }}"
                        onsubmit="return loginValidation(this);" autocomplete="off">
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
