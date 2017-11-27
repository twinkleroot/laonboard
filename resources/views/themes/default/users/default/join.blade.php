@extends("themes.". cache('config.theme')->name. ".layouts.basic")

@section('title')회원가입약관 | {{ cache("config.homepage")->title }}@endsection

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
<!-- auth login -->
    <div class="panel panel-default">
        <div class="panel-heading bg-sir">
            <h3 class="panel-title">회원가입약관</h3>
        </div>
        <div class="panel-body row">
            <div class="col-md-12 mb15">
                <div class="help bg-info">
                    회원가입약관 및 개인정보처리방침안내의 내용에 동의하셔야 회원가입 하실 수 있습니다.
                </div>
            </div>
            <form class="contents col-md-12" id="userForm" name="userForm" role="form" method="POST" action="{{ route('user.register.form') }}">
            {{ csrf_field() }}
                <section id="fregister_term">
                    <h2>회원가입약관</h2>
                    <div class="form-group mg5">
                        <textarea readonly>{{ cache('config.join')->stipulation }}</textarea>
                        <fieldset class="fregister_agree">
                            <label for="agreeStipulation">회원가입약관의 내용에 동의합니다.</label>
                            <input type="checkbox" id="agreeStipulation" name="agreeStipulation" value="1" >
                        </fieldset>
                    </div>
                </section>
                <section id="fregister_private">
                    <h2>개인정보처리방침안내</h2>
                    <div class="form-group mg5">
                        <textarea readonly>{{ cache('config.join')->privacy }}</textarea>
                        <fieldset class="fregister_agree">
                            <label for="agreePrivacy">개인정보처리방침안내의 내용에 동의합니다.</label>
                            <input type="checkbox" id="agreePrivacy" name="agreePrivacy" value="1" >
                        </fieldset>
                    </div>
                </section>
                <div class="form-group col-md-12">
                    <button type="submit" class="btn btn-block btn-sir">회원가입</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>
@endsection
