@extends("themes.". cache('config.theme')->name. ".layouts.basic")

@section('title')회원가입 | {{ cache("config.homepage")->title }}@endsection

@section('include_css')
<link rel="stylesheet" type="text/css" href="{{ ver_asset("themes/default/css/common.css") }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset("themes/default/css/auth.css") }}">
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
        <form class="contents col-md-8 col-md-offset-2" id="userForm" name="userForm" role="form" method="POST" action="{{ route('user.register') }}" onsubmit="return onsubmit;">
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

            {{ fireEvent('registerForm') }}

            <div class="form-group">
                <button type="button" class="btn btn-block btn-sir submitBtn">회원가입</button>
            </div>

            @if(cache('config.sns')->naverKey || cache('config.sns')->kakaoKey || cache('config.sns')->facebookKey || cache('config.sns')->googleKey)
                <!-- 소셜로그인 -->
                <div class="mdline-title">
                    <div class="text-center">
                        <span>Social Login</span>
                    </div>
                </div>

                <div class="social-login">
                    @if(cache('config.sns')->naverKey)
                    <!-- 네이버로 로그인 -->
                    <a href="{{ route('social', 'naver') }}" class="btn btn-block btn-naver">
                        <div class="icon icon-naver"></div>
                        <span class="text-left">네이버로 회원가입</span>
                    </a>
                    @endif
                    @if(cache('config.sns')->kakaoKey)
                    <!-- 카카오톡으로 로그인 -->
                    <a href="{{ route('social', 'kakao') }}" class="btn btn-block btn-kakao">
                        <div class="icon icon-kakao"></div>
                        <span class="text-left">카카오톡으로 회원가입</span>
                    </a>
                    @endif
                    @if(cache('config.sns')->facebookKey)
                    <!-- 페이스북으로 로그인 -->
                    <a href="{{ route('social', 'facebook') }}" class="btn btn-block btn-facebook">
                        <div class="icon icon-facebook"></div>
                        <span class="text-left">페이스북으로 회원가입</span>
                    </a>
                    @endif
                    @if(cache('config.sns')->googleKey)
                    <!-- 구글로 로그인 -->
                    <a href="{{ route('social', 'google') }}" class="btn btn-block btn-google">
                        <div class="icon icon-google"></div>
                        <span class="text-left">구글로 회원가입</span>
                    </a>
                    @endif
                </div>
                @endif
                <!-- 소셜로그인 end -->

            {{ fireEvent('captchaPlace') }}

        </form>
    </div>
    @endif
</div>
</div>
</div>
</div>
<script>
var onsubmit = function() {

    if(!filterNickname()) {
        return false;
    }
    return true;
}
</script>

{{ fireEvent('registerUserEnd') }}

@endsection
