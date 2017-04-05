@extends('theme')

@section('title')
    소셜 로그인 완료 | LaBoard
@endsection

@section('include_script')
<script>
$(function(){
    $('#continue').click(function(){
        $('#social_form').attr('action', '{{ route('social.continue') }}')
        $('#social_form').submit();
    });
    $('#showLoginForm').click(function(){
        $('#loginForm').show();
    });
    $('#connectExistAccount').click(function(){
        $('#social_form').attr('action', '{{ route('social.connectExistAccount') }}')
        $('#social_form').submit();
    });
});
</script>
@endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{ $userFromSocial->getNickname() }}님 환영합니다.</div>

                <form id="social_form" method="POST" action="">
                    {{ csrf_field() }}
                    <div class="panel-body">
                        <input type="button" id="continue" value="계속 하기" />
                        <input type="button" id="showLoginForm" value="기존 계정과 연결" />
                    </div>
                    <div class="panel-body" id="loginForm" style="display:none">
                        <p>
                            기존 이메일 <input type="text" name="email"/>
                            비밀번호 <input type="password" name="password"/>
                        </p>
                        <input type="button" id="connectExistAccount" value="연결하고 로그인하기" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
