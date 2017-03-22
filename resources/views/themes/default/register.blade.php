@extends('themes.default.basic')
@section('title')
    회원가입
@endsection

@section('include_script')
    <script src='https://www.google.com/recaptcha/api.js'></script>
@endsection

@section('content')
<div class="container">
<div class="row">
<!-- auth register -->
<div class="col-md-6 col-md-offset-3">
    <div id="auth">
        <div class="header">
            <h1>회원가입</h1>
        </div>
        <form class="form-horizontal" role="form" method="POST" action="{{ route('register.reCaptcha') }}">
            {{ csrf_field() }}

            <div class="form-box">
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label>
                        <span class="sr-only">이메일</span>
                        <input id="email" class="form-control col-xs-12" type="email" name="email" value="{{ old('email') }}" placeholder="이메일을 입력하세요" required autofocus="">
                    </label>

                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label>
                        <span class="sr-only">비밀번호</span>
                        <input id="password" class="form-control col-xs-12" type="password" name="password" placeholder="비밀번호를 입력하세요" required>
                    </label>

                    @if ($errors->has('password'))
                        <span class="help-block">
                          <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <label>
                        <span class="sr-only">비밀번호 확인</span>
                        <input id="password-confirm" class="form-control col-xs-12" type="password" name="password_confirmation" placeholder="비밀번호를 다시 입력하세요" required>
                    </label>
                </div>

                <div class="form-group{{ $errors->has('nick') ? ' has-error' : '' }}">
                    <label>
                        <span class="sr-only">닉네임</span>
                        <input id="nick" class="form-control col-xs-12" type="text" name="nick" value="{{ old('nick') }}" placeholder="닉네임을 입력하세요" required autofocus>
                    </label>

                    @if ($errors->has('nick'))
                        <span class="help-block">
                            <strong>{{ $errors->first('nick') }}</strong>
                        </span>
                    @endif

                    <p class="form-comment">
                        공백없이 한글, 영문, 숫자만 입력 가능<br>
                        (한글2자, 영문4자 이상)<br>
                        닉네임을 바꾸시면 0일 이내에는 변경할 수 없습니다
                    </p>
                </div>

            </div>

            <div class="form-group{{ $errors->has('reCapcha') ? ' has-error' : '' }}">
                <div class="g-recaptcha col-md-6" data-sitekey="6LcKohkUAAAAANcgIst0HFMMT81Wq5HIxpiHhXGZ">
                </div>
                    @if ($errors->has('reCapcha'))
                        <span class="help-block">
                            <strong>{{ $errors->first('reCapcha') }}</strong>
                        </span>
                    @endif
            </div>



            <div class="form-group">
                <button type="submit" class="join col-xs-12">회원가입</button>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection