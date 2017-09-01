@extends('layout.'. cache('config.skin')->layout. '.basic')

@section('title')
    회원 비밀번호 확인 | {{ cache("config.homepage")->title }}
@endsection

@section('include_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/auth.css') }}">
@endsection

@section('content')
@if(Session::has('message'))
<div class="alert alert-info">
    {{Session::get('message') }}
</div>
@endif
<div class="container">
<div class="row">
<div class="col-md-6 col-md-offset-3 col-xs-10 col-xs-offset-1">
    <div class="panel panel-default">
        <div class="panel-heading bg-sir">
            <h3 class="panel-title">회원 비밀번호 확인</h3>
        </div>

        <div class="panel-body row">
            <form class="contents col-md-8 col-md-offset-2" role="form" method="POST" action="{{ route('user.confirmPassword') }}">
            {{ csrf_field() }}
            <input type="hidden" name="work" value="{{ $work }}" />
                <div class="form-group mg5">
                    <label for="email" class="control-label">회원 이메일</label>
                    <span style="margin-left: 7px;">{{ $email }}</span>
                </div>

                <div class="form-group">
                    <label for="password" class="control-label">비밀번호</label>
                    <input id="password" type="password" class="form-control" name="password" required>
                </div>

                <div class="form-group">
                    <div>
                        <button type="submit" class="btn btn-sir">확인</button>
                        <a class="btn btn-sir" href="{{ route('home') }}">메인으로 돌아가기</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>
@endsection
