@extends('layouts.default.basic')

@section('title')
    비밀번호 재설정 | {{ Cache::get("config.homepage")->title }}
@endsection

@section('include_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/auth.css') }}">
@endsection

@section('content')
<div class="container">
<div class="row">
<div class="col-md-6 col-md-offset-3 col-xs-10 col-xs-offset-1">

<!-- auth password reset -->
    <div class="panel panel-default">
        <div class="panel-heading bg-sir">
            <h3 class="panel-title">비밀번호 재설정</h3>
        </div>

        <div class="panel-body row">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <form class="contents col-md-8 col-md-offset-2" role="form" method="POST" action="{{ route('password.email') }}">
            {{ csrf_field() }}

                <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email"><span class="sr-only">이메일</span></label>
                    <input type="email" class="form-control sr-only-input" id="email" name="email" value="{{ old('email') }}" placeholder="이메일 주소를 입력하세요" required>

                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-lg btn-block btn-sir">
                        비밀번호 재설정 연결 메일 보내기
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>
@endsection
