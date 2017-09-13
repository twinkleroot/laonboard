@extends('layout.'. cache('config.skin')->layout. '.basic')

@section('title')
    회원가입약관 | {{ cache("config.homepage")->title }}
@endsection

@section('include_css')
<link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/common.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/auth.css') }}">
@endsection

@section('include_script')
<script src='https://www.google.com/recaptcha/api.js' async defer></script>
<script src="{{ url('js/certify.js') }}"></script>
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

<!-- auth login -->
<div class="panel panel-default">
    <div class="panel-heading bg-sir">
        <h3 class="panel-title">회원가입약관</h3>
    </div>
    <div class="panel-body row">
        <form class="contents col-md-8 col-md-offset-2" id="userForm" name="userForm" role="form" method="POST" action="{{ route('user.register.form') }}">
            {{ csrf_field() }}
            <p>
                회원가입약관
            </p>
            <textarea>{{ cache('config.join')->stipulation }}</textarea>
            <label for="agreeStipulation">회원가입약관의 내용에 동의합니다.</label>
            <input type="checkbox" value="1" id="agreeStipulation" name="agreeStipulation"/>
            <p>
                개인정보처리방침안내
            </p>
            <textarea>{{ cache('config.join')->privacy }}</textarea>
            <label for="agreePrivacy">개인정보처리방침안내의 내용에 동의합니다.</label>
            <input type="checkbox" value="1" id="agreePrivacy" name="agreePrivacy"/>

            <div class="form-group">
                <button type="submit" class="btn btn-block btn-sir">회원가입</button>
            </div>
        </form>
    </div>
</div>

</div>
</div>
</div>
@endsection
