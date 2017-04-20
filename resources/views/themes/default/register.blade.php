@extends('theme')
@section('title')
    회원가입
@endsection

@section('include_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/auth.css') }}">
@endsection

@section('include_script')
    <script src='https://www.google.com/recaptcha/api.js'></script>
@endsection

@section('content')
<div class="container">
<div class="row">
<div class="col-md-6 col-md-offset-3 col-xs-12">

<!-- auth login -->
    <div class="panel panel-default">
        <div class="panel-heading bg-sir">
            <h3 class="panel-title">회원가입</h3>
        </div>
        <div class="panel-body row">
            <form class="contents col-md-8 col-md-offset-2" role="form" method="POST" action="{{ route('user.register') }}">
            {{ csrf_field() }}
                <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email">이메일</label>
                    <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="이메일 주소를 입력하세요" required autofocus>

                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="password">비밀번호</label>
                    <input id="password" type="password" name="password" class="form-control" placeholder="비밀번호를 입력하세요" required>

                    @if ($errors->has('password'))
                        <span class="help-block">
                          <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="password">비밀번호 확인</label>
                    <input id="password-confirm" type="password" name="password_confirmation" class="form-control" placeholder="비밀번호를 한번 더 입력하세요" required>
                </div>

                <div class="form-group {{ $errors->has('nick') ? ' has-error' : '' }}">
                    <label for="nick">닉네임</label>
                    <input id="nick" type="text" name="nick" class="form-control" value="{{ old('nick') }}" placeholder="닉네임을 입력하세요" required>
                    <p class="help-block">
                        공백없이 한글, 영문, 숫자만 입력 가능<br>
                        (한글2자, 영문4자 이상)<br>
                        닉네임을 바꾸시면 0일 이내에는 변경할 수 없습니다
                    </p>

                    @if ($errors->has('nick'))
                        <span class="help-block">
                            <strong>{{ $errors->first('nick') }}</strong>
                        </span>
                    @endif
                </div>

                <!-- 리캡챠 -->
                <div class="form-group {{ $errors->has('reCaptcha') ? ' has-error' : '' }}" style="height:80px;">
                    <div class="g-recaptcha" data-sitekey="6LcKohkUAAAAANcgIst0HFMMT81Wq5HIxpiHhXGZ"></div>
                        @if ($errors->has('reCaptcha'))
                            <span class="help-block">
                                <strong>{{ $errors->first('reCaptcha') }}</strong>
                            </span>
                        @endif
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-lg btn-block btn-sir">회원가입</button>
                </div>
            </form>
        </div>
    </div>

</div>
</div>
</div>
@endsection
