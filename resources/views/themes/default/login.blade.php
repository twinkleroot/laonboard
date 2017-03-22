@extends('theme')
@section('title')
    로그인
@endsection

@section('content')
<div class="container">
<div class="row">
<!-- auth login -->
<div class="col-md-6 col-md-offset-3">
    <div id="auth">
        <div class="header">
            <h1>로그인</h1>
        </div>
        <form class="form-horizontal" role="form" method="POST" action="{{ route('login') }}">
            {{ csrf_field() }}

            <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                <label>
                    <span class="sr-only">이메일</span>
                    <input id="email" class="form-control col-xs-12" type="email" name="email" value="{{ old('email') }}" placeholder="이메일을 입력하세요" required autofocus>
                </label>

                @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                <label class="">
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
            <div class="form-control col-xs-12">
            <div class="row">
                <label>
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span class="check-span">자동 로그인</span>
                </label>

                <a href="{{ route('password.request') }}" class="link-right">비밀번호 찾기</a>
            </div>
            </div>
            </div>

            <div class="form-group">
                <button type="submit" class="login col-xs-12">로그인</button>
            </div>

            <div class="form-group">
                <p class="register">아직 회원이 아니신가요? <a href="{{ route('register') }}">회원가입</a> </p>
            </div>

            <div class="form-group">
                <div class="col-md-8 col-md-offset-4">
                    <a class="btn btn-primary" href="{{ route('social.naver') }}">
                        네이버로 로그인
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
