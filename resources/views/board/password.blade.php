@extends('theme')

@section('title')
    LaBoard | {{ App\Config::getConfig('config.homepage')->title }}
@endsection

@section('content')
<div class="container">
<div class="row">
<div class="col-md-6 col-md-offset-3 col-xs-10 col-xs-offset-1">

<!-- user confirm password -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{ $subject }}</h3>
        </div>
        <div class="panel-heading bg-sir">
            <b>비밀글 기능으로 보호된 글입니다.</b><br />
            작성자와 관리자만 열람하실 수 있습니다. 본인이라면 비밀번호를 입력하세요.
        </div>

        <div class="panel-body row">
            <form class="contents col-md-8 col-md-offset-2" role="form" method="POST" action="{{ route('board.validatePassword', $boardId) }}">
                {{ csrf_field() }}
                <input type="hidden" class="form-control" name="writeId" value="{{ $writeId }}" required>
                <input type="hidden" class="form-control" name="nextUrl" value="{{ Session::get('nextUrl') }}" required>
                <div class="form-group">
                    <label for="password" class="control-label">비밀번호</label>
                    <input id="password" type="password" class="form-control" name="password" required>
                </div>

                <div class="form-group">
                    <div>
                        <button type="submit" class="btn btn-sir">확인</button>
                        <button type="button" class="btn btn-sir" onclick="history.back();">돌아가기</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>
@endsection
