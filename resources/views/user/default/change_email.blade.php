@extends('layout.'. cache('config.skin')->layout. '.basic')

@section('title')메일인증 메일주소 변경 | {{ cache("config.homepage")->title }}@endsection

@section('include_css')
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/common.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/auth.css') }}">
@endsection

@section('include_script')
<script src='https://www.google.com/recaptcha/api.js' async defer></script>
@endsection

@section('content')
<div class="container">
<div class="row">
<div class="col-md-6 col-md-offset-3">
    <div class="panel panel-default">
        <script src="{{ ver_asset('js/postcode.js') }}"></script>
        <div class="panel-heading bg-sir">
            <h3 class="panel-title">메일인증 메일주소 변경</h3>
        </div>
        <div class="panel-body row">
            <form class="contents col-md-10 col-md-offset-1" id="emailForm" role="form" method="POST" action="{{ route('user.email.update') }}">
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

                <div class="form-group">
                    <button type="button" class="btn btn-sir" onclick="validate();">인증메일변경</button>
                    <a class="btn btn-sir" href="{{ route('home') }}">취소</a>
                </div>
                <!-- 리캡챠 -->
                <div id='recaptcha' class="g-recaptcha"
                    data-sitekey="{{ cache('config.sns')->googleRecaptchaClient }}"
                    data-callback="onSubmit"
                    data-size="invisible" style="display:none">
                </div>
                <input type="hidden" name="g-recaptcha-response" id="g-response" />
            </form>
        </div>
    </div>
</div>
</div>
</div>
<script>
function onSubmit(token) {
    $("#g-response").val(token);
    $("#emailForm").submit();
}
function validate(event) {
    grecaptcha.execute();
}
</script>
@endsection
