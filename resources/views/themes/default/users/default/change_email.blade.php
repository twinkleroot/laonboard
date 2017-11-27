@extends("themes.". cache('config.theme')->name. ".layouts.basic")

@section('title')인증메일주소변경 | {{ cache("config.homepage")->title }}@endsection

@section('include_css')
<link rel="stylesheet" type="text/css" href="{{ ver_asset("themes/default/css/common.css") }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset("themes/default/css/auth.css") }}">
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
                    <button type="button" class="btn btn-sir submitBtn">인증메일변경</button>
                    <a class="btn btn-sir" href="{{ route('home') }}">취소</a>
                </div>

                {{ fireEvent('captchaPlace') }}

            </form>
        </div>
    </div>
</div>
</div>
</div>
@endsection
