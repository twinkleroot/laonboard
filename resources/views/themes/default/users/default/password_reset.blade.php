@extends("themes.". cache('config.theme')->name. ".layouts.basic")

@section('title')비밀번호재설정 | {{ cache("config.homepage")->title }}@endsection

@section('include_css')
<link rel="stylesheet" type="text/css" href="{{ ver_asset("themes/default/css/common.css") }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset("themes/default/css/auth.css") }}">
@endsection

@section('content')
@if($errors->any())
<script>
    alert("{{ $errors->first() }}");
</script>
@endif
<div class="container">
<div class="row">
<div class="col-md-6 col-md-offset-3">

<!-- auth password reset -->
    <div class="panel panel-default">
        <div class="panel-heading bg-sir">
            <h3 class="panel-title">비밀번호 재설정</h3>
        </div>

        <div class="panel-body row">
            <form class="contents col-md-8 col-md-offset-2" role="form" method="POST" action="{{ route('reset.store') }}">
            {{ csrf_field() }}

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email">이메일</label>
                    <input id="email" type="email" class="form-control" name="email" placeholder="이메일 주소를 입력하세요" value="{{ $email or old('email') }}">

                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="password">비밀번호</label>
                    <input id="password" type="password" class="form-control" name="password" placeholder="새 비밀번호를 입력하세요" required>

                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                    <label for="password-confirm">비밀번호 확인</label>
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="비밀번호를 다시 입력하세요" required>

                    @if ($errors->has('password_confirmation'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-block btn-sir">
                        비밀번호 재설정
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>
@endsection
