@extends("themes.". cache('config.theme')->name. ".layouts.basic")

@section('title')소셜로그인 | {{ cache("config.homepage")->title }}@endsection

@section('include_css')
<link rel="stylesheet" type="text/css" href="{{ ver_asset("themes/default/css/common.css") }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset("themes/default/css/auth.css") }}">
@endsection

@section('content')
<div class="container">
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading bg-sir">
                <h3 class="panel-title">{{ $userFromSocial->getNickname() ? : '손' }}님, 새로 가입하시겠습니까?</h3>
            </div>
            <div class="panel-body">
                <form class="contents col-md-12" method="POST" action="{{ route('social.socialUserJoin') }}" onsubmit="return joinValidation(this);" autocomplete="off">
                    {{ csrf_field() }}
                    <input type="hidden" name="provider" value="{{ $provider }}" />
                    <div class="form-group">
                        <label for="password">비밀번호</label>
                        <input type="password" name="password" class="form-control" minlength="3" maxlength="20" placeholder="비밀번호를 입력하세요" required />
                        <p class="help-block">
                            {{ $message['password'] }}
                        </p>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">비밀번호 확인</label>
                        <input type="password" name="password_confirmation" class="form-control" minlength="3" maxlength="20" placeholder="비밀번호를 한번 더 입력하세요" required />
                    </div>
                    <div class="form-group">
                        <label for="email">이메일</label>
                        <input type="email" name="email" class="form-control" value="{{ $userFromSocial->email }}" required />
                        @if(array_has($message, 'email'))
                            <span class="help-block">
                                <strong>{{ $message['email'] }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group" class="sr-only">
                        <label for="nick">닉네임</label>
                        <input type="text" name="nick" class="form-control" value="{{ $userFromSocial->nickname }}" placeholder="이메일 주소를 입력하세요" required />
                        @if(array_has($message, 'nick'))
                            <span class="help-block">
                                <strong>{{ $message['nick'] }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <input type="submit" id="userJoin" class="btn btn-block btn-sir" placeholder="닉네임을 입력하세요" value="회원가입"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading bg-sir">
                <h3 class="panel-title">기존 계정과 연결하시겠습니까?</h3>
            </div>
            <div class="panel-body">
                <form method="POST" action="{{ route('social.connectExistAccount') }}" onsubmit="return loginValidation(this);" autocomplete="off">
                    {{ csrf_field() }}
                    <input type="hidden" name="provider" value="{{ $provider }}" />
                    <div class="form-group">
                        <label for="email">기존 이메일</label>
                        <input type="text" name="email" class="form-control" maxlength="20" placeholder="기존에 가입된 이메일 주소를 입력하세요" required />
                    </div>
                    <div class="form-group">
                        <label for="password">비밀번호</label>
                        <input type="password" name="password" class="form-control" maxlength="20" placeholder="비밀번호를 입력하세요" required />
                    </div>
                    <div class="form-group">
                        <input type="submit" id="connectExistAccount" class="btn btn-block btn-sir" value="연결하고 로그인하기"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

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
        alert('비밀번호와 비밀번호 확인이 같지 않습니다.');
        form.password_confirmation.focus();
        return false;
    }

    if(form.email.value == '') {
        alert('이메일을 입력해 주세요.');
        form.email.focus();
        return false;
    }

    if(form.nick.value == '') {
        alert('닉네임을 입력해 주세요.');
        form.nick.focus();
        return false;
    }

    if(!requestValidate(form)) {
        return false;
    }

    return true;
}

function requestValidate(form) {
    var data = {
        'email' : form.email.value,
        'nick' : form.nick.value,
        'password' : form.password.value,
        'password_confirmation' : form.password_confirmation.value,
        '_token' : '{{ csrf_token() }}'
    };

    var message = new Array();
    var result = true;
    $.ajax({
        url: '/register/validate',
        type: 'POST',
        data: data,
        dataType: 'json',
        async: false,
        cache: false,
        success: function(data) {
            message = data.message;

            if(message.length > 0) {
                alert(message[0]);
                result = false;
            }
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

    if(checkExistData('email', form.email.value) == false) {
        alert('가입되지 않은 이메일입니다. 확인 후 다시 입력해 주세요.');
        form.email.focus();
        return false;
    }

    return true;
}
</script>
@endsection
