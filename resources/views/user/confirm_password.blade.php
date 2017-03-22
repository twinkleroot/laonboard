@extends('theme')

@section('title')
    LaBoard | 회원 비밀번호 확인
@endsection

@section('content')
@if(Session::has('message'))
  <div class="alert alert-info">
    {{Session::get('message') }}
  </div>
@endif
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">회원 비밀번호 확인</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('user.confirmPassword') }}">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="email" class="col-md-4 control-label">회원 이메일</label>
                            <div class="col-md-6">
                                {{ $email }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password" class="col-md-4 control-label">비밀번호</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    확인
                                </button>
                                <a class="btn btn-primary" href="{{ route('index') }}">메인으로 돌아가기</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
