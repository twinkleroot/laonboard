@extends('theme')

@section('title')
    메일인증 메일주소 변경 | {{ Cache::get("config.homepage")->title }}
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
    <div class="panel panel-default">
        <script src="{{ asset('js/postcode.js') }}"></script>
        <div class="panel-heading bg-sir">
            <h3 class="panel-title">메일인증 메일주소 변경</h3>
        </div>
        <div class="panel-body row">
            <form class="contents col-md-10 col-md-offset-1" role="form" method="POST" action="{{ route('user.email.update') }}">
            {{ csrf_field() }}
            {{ method_field('PUT') }}
                <input type="hidden" name="beforeEmail" value="{{ $email }}" />

                <div class="panel-heading">
                    <p class="heading-p">
                        <span class="heading-span">사이트 이용정보 입력</span>
                    </p>
                </div>

                <div class="form-group">
                    <label for="email_readonly">이메일</label>
                    <input type="email" class="form-control" name="email" value="{{ $email }}">
                </div>

                <!-- 리캡챠 -->
                <div class="form-group{{ $errors->has('reCaptcha') ? ' has-error' : '' }}">
                    <div class="g-recaptcha" data-sitekey="6LcKohkUAAAAANcgIst0HFMMT81Wq5HIxpiHhXGZ"></div>
                    @if ($errors->has('reCaptcha'))
                        <span class="help-block">
                            <strong>{{ $errors->first('reCaptcha') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-sir">인증메일변경</button>
                    <a class="btn btn-sir" href="{{ route('index') }}">취소</a>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>
@endsection
