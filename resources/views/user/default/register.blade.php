@extends('layout.'. cache('config.skin')->layout. '.basic')

@section('title')회원가입 | {{ cache("config.homepage")->title }}@endsection

@section('include_css')
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/common.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/auth.css') }}">
@endsection

@section('include_script')
<script src='https://www.google.com/recaptcha/api.js' async defer></script>
<script src="{{ url('js/certify.js') }}"></script>
@endsection

@section('content')
@if($errors->any())
<div class="alert alert-info">
    {{ $errors->first() }}
</div>
@endif
<div class="container">
<div class="row">
<div class="col-md-6 col-md-offset-3">
<!-- auth login -->
<div class="panel panel-default">
    <div class="panel-heading bg-sir">
        <h3 class="panel-title">회원가입</h3>
    </div>

    @if( (isset($agreePrivacy) && !$agreePrivacy) || (isset($agreeStipulation) && !$agreeStipulation))
    <div class="panel-body row">
        <div class="contents col-md-8 col-md-offset-2">
            <p>회원가입 약관과 개인정보처리방침안내에 동의하셔야 회원가입을 계속 진행할 수 있습니다.</p>
            <div class="form-group">
                <a href="{{ route('user.join')}}" class="btn btn-block btn-sir">뒤로 가기</a>
            </div>
        </div>
    </div>
    @else
    <div class="panel-body row">
        <form class="contents col-md-8 col-md-offset-2" id="userForm" name="userForm" role="form" method="POST" action="{{ route('user.register') }}">
        @if(cache('config.cert')->certHp)
            <input type="hidden" name="certType" value="">
            <input type="hidden" name="name" value="">
            <input type="hidden" name="hp" value="">
            <input type="hidden" name="certNo" value="">
        @endif
            {{ csrf_field() }}
            <input type="hidden" name="agreePrivacy" value="{{ isset($agreePrivacy) ? $agreePrivacy : old('agreePrivacy') }}">
            <input type="hidden" name="agreeStipulation" value="{{ isset($agreeStipulation) ? $agreeStipulation : old('agreeStipulation') }}">
            <input type="hidden" name="passwordMessage" value="{{ $passwordMessage }}">

            <div class="form-group @if($errors->has('email'))has-error @endif">
                <label for="email">이메일</label>
                <input id="email" type="email" name="email" class="form-control" value="{{ isset($email) ? $email : old('email') }}" placeholder="이메일 주소를 입력하세요" required autofocus>

                @if ($errors->has('email'))
                <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
                @endif
            </div>

            <div class="form-group @if($errors->has('password'))has-error @endif">
                <label for="password">비밀번호</label>
                <input id="password" type="password" name="password" class="form-control" placeholder="비밀번호를 입력하세요" required>

                @if ($errors->has('password'))
                <span class="help-block">
                  <strong>{{ $errors->first('password') }}</strong>
                </span>
                @else
                <p class="help-block">
                    {{ $passwordMessage }}
                </p>
                @endif
            </div>

            <div class="form-group @if($errors->has('password_confirmation'))has-error @endif">
                <label for="password">비밀번호 확인</label>
                <input id="password-confirm" type="password" name="password_confirmation" class="form-control" placeholder="비밀번호를 한번 더 입력하세요" required>

                @if ($errors->has('password_confirmation'))
                <span class="help-block">
                  <strong>{{ $errors->first('password_confirmation') }}</strong>
                </span>
                @endif
            </div>

            <div class="form-group @if($errors->has('nick'))has-error @endif">
                <label for="nick">닉네임</label>
                <input id="nick" type="text" name="nick" class="form-control" value="{{ isset($nick) ? $nick : old('nick') }}" placeholder="닉네임을 입력하세요" required>
                @if ($errors->has('nick'))
                <span class="help-block">
                    <strong>{{ $errors->first('nick') }}</strong>
                </span>
                @endif
                <p class="help-block">
                    {{-- 공백없이 한글, 영문, 숫자만 입력 가능<br> --}}
                    {{-- (한글2자, 영문4자 이상, Emoji 포함 가능)<br> --}}
                    (한글2자, 영문4자 이상)<br>
                    닉네임을 정하시면 {{ cache("config.join")->nickDate }}일 이내에는 변경할 수 없습니다
                </p>
            </div>

            {{-- @if(cache('config.cert')->certIpin)
            <div class="form-group">
                <button type="button" class="btn btn-block btn-sir" id="win_ipin_cert">아이핀 본인확인</button>

                @if ($errors->has('ipin'))
                <span class="help-block">
                  <strong>{{ $errors->first('ipin') }}</strong>
                </span>
                @endif
            </div>
            @endif --}}
            @if(cache('config.cert')->certReq && cache('config.cert')->certHp)
            <div class="form-group @if($errors->has('hpCert'))has-error @endif">
                <button type="button" class="btn btn-block btn-sir" id="win_hp_cert">휴대폰 본인확인</button>

                @if($errors->has('hpCert'))
                <span class="help-block">
                    <strong>{{ $errors->first('hpCert') }}</strong>
                </span>
                @endif
            </div>
            @endif
            <div class="form-group">
                <button type="button" class="btn btn-block btn-sir" onclick="validate();">회원가입</button>
            </div>
            <div id='recaptcha' class="g-recaptcha"
                data-sitekey="{{ cache('config.sns')->googleRecaptchaClient }}"
                data-callback="onSubmit"
                data-size="invisible" style="display:none">
            </div>
            <input type="hidden" name="g-recaptcha-response" id="g-response" />
        </form>
    </div>
    @endif
</div>

</div>
</div>
</div>
<script>
function onSubmit(token) {
    $("#g-response").val(token);
    $("#userForm").submit();
}

function validate(event) {
    if(userSubmit()) {
        grecaptcha.execute();
    }
}

function userSubmit() {
    var nick = "";

    $.ajax({
        url: '/ajax/filter/user',
        type: 'post',
        data: {
            '_token' : '{{ csrf_token() }}',
            'nick' : $('#nick').val()
        },
        dataType: 'json',
        async: false,
        cache: false,
        success: function(data) {
            nick = data.nick;
        }
    });

    if(nick) {
        alert("닉네임에 금지단어 (" + nick + ") 가 포함되어 있습니다.");
        $('#nick').focus();
        return false;
    }

    return true;
}

$(function() {
    // 아이핀인증
    // $("#win_ipin_cert").click(function() {
    //     if(!cert_confirm())
    //         return false;
    //
    //     var url = "http://ahn13.gnutest.com/gnu5/plugin/okname/ipin1.php";
        {{-- var url = "{{ route('cert.kcb.ipin') }}"; --}}
    //     certify_win_open('kcb-ipin', url);
    //     return;
    // });

    // 휴대폰인증
    $("#win_hp_cert").click(function() {
        if(!cert_confirm())
            return false;

        @if(cache('config.cert')->certHp == 'kcb')
            certify_win_open("kcb-hp", "{{ route('cert.kcb.hp1')}}");
        @endif

        return;
    });
});
</script>
@endsection
