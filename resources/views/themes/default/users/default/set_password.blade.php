@extends("themes.". cache('config.theme')->name. ".layouts.basic")

@section('title')최초비밀번호설정 | {{ cache("config.homepage")->title }}@endsection

@section('include_css')
<link rel="stylesheet" type="text/css" href="{{ ver_asset("themes/default/css/common.css") }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset("themes/default/css/auth.css") }}">
@endsection

@section('content')
@if(Session::has('message'))
<div class="alert alert-info">
    {{Session::get('message') }}
</div>
@endif
<div class="container">
<div class="row">
<div class="col-md-6 col-md-offset-3">

<!-- user password setting -->
    <div class="panel panel-default">
        <div class="panel-heading bg-sir">
            <h3 class="panel-title">최초 비밀번호 설정</h3>
        </div>
        <div class="panel-body row">
            <form class="contents col-md-8 col-md-offset-2" role="form" method="POST" action="{{ route('user.setPassword') }}">
            {{ csrf_field() }}

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="password" class="col-md-4 control-label">비밀번호</label>
                    <div class="col-md-6">
                        <input id="password" type="password" class="form-control" name="password" required>
                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="col-md-4 control-label">비밀번호 확인</label>
                    <div class="col-md-6">
                        <input id="password" type="password" class="form-control" name="password_confirmation" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            확인
                        </button>
                    </div>
                </div>
            </form>
         </div>
    </div>
</div>
</div>
</div>
@endsection
