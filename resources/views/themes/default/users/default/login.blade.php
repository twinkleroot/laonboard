@extends("themes.". cache('config.theme')->name. ".layouts.basic")

@section('title')로그인 | {{ cache("config.homepage")->title }}@endsection

@section('include_css')
<link rel="stylesheet" type="text/css" href="{{ ver_asset("themes/default/css/common.css") }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset("themes/default/css/auth.css") }}">
@endsection

@section('content')
<div class="container">
<div class="row">
<div class="col-md-6 col-md-offset-3">

<!-- auth login -->
    <div class="panel panel-default">
        <div class="panel-heading bg-sir">
            <h3 class="panel-title">로그인</h3>
        </div>
        <div class="panel-body row">
            <form class="contents col-md-8 col-md-offset-2" role="form" method="POST" action="{{ route('login') }}">
            {{ csrf_field() }}

                <div class="form-group mg5 {{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email"><span class="sr-only">이메일</span></label>
                    <input type="email" class="form-control sr-only-input" id="email" name="email" value="@if(isDemo()) {{ config('demo.email') }}@else{{ old('email') }}@endif" placeholder="이메일 주소를 입력하세요" required autofocus>

                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group mg5 {{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="password"><span class="sr-only">비밀번호</span></label>
                    <input type="password" class="form-control sr-only-input" id="password" name="password" @if(isDemo()) value="{{ config('demo.password') }}" @endif placeholder="비밀번호를 입력하세요" required>

                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="row">
                    <div class="col-xs-7">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="remember"  {{ old('remember') ? 'checked' : '' }}>
                            자동 로그인
                        </label>
                    </div>
                    </div>

                    <div class="col-xs-5">
                    <div class="password_search">
                        <p class="text-right">
                            <a href="{{ route('remind.create') }}">비밀번호 재설정</a>
                        </p>
                    </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-block btn-sir">로그인</button>
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
                        <span class="text-left">네이버로 로그인</span>
                    </a>
                    @endif
                    @if(cache('config.sns')->kakaoKey)
                    <!-- 카카오톡으로 로그인 -->
                    <a href="{{ route('social', 'kakao') }}" class="btn btn-block btn-kakao">
                        <div class="icon icon-kakao"></div>
                        <span class="text-left">카카오톡으로 로그인</span>
                    </a>
                    @endif
                    @if(cache('config.sns')->facebookKey)
                    <!-- 페이스북으로 로그인 -->
                    <a href="{{ route('social', 'facebook') }}" class="btn btn-block btn-facebook">
                        <div class="icon icon-facebook"></div>
                        <span class="text-left">페이스북으로 로그인</span>
                    </a>
                    @endif
                    @if(cache('config.sns')->googleKey)
                    <!-- 구글로 로그인 -->
                    <a href="{{ route('social', 'google') }}" class="btn btn-block btn-google">
                        <div class="icon icon-google"></div>
                        <span class="text-left">구글로 로그인</span>
                    </a>
                    @endif
                </div>
                @endif
                <!-- 소셜로그인 end -->
            </form>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="text-center">
                아직 회원이 아니신가요? <a href="{{ route('user.join') }}">회원가입</a>
            </div>
        </div>
    </div>

</div>
</div>
</div>
@endsection
