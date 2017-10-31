@extends("themes.default.layouts.". ($board->layout ? : 'basic'))

@section('title')비밀번호 확인 | {{ Cache::get("config.homepage")->title }}@endsection

@section('include_css')
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/common.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/board.css') }}">
@endsection

@section('content')
<div class="container">
<div class="row">
<div class="col-md-6 col-md-offset-3 col-xs-10 col-xs-offset-1">
<!-- user confirm password -->
    <div class="panel panel-default">
        @if($subject)
        <div class="panel-heading bg-sir">
            <h3 class="panel-title">{{ $subject }}</h3>
        </div>
        @endif
        <div style="padding: 15px 15px 0;">
        <div class="help bg-info">
            @if($type == 'secret')
                <b>비밀글 기능으로 보호된 글입니다.</b><br />
                작성자와 관리자만 열람하실 수 있습니다. 본인이라면 비밀번호를 입력하세요.
            @else
                <b>작성자만 글을 {{ strpos(strtolower($type), 'delete') ? '삭제' : '수정' }}할 수 있습니다.</b><br />
                작성자 본인이라면, 글 작성시 입력한 비밀번호를 입력하여 글을 {{ strpos(strtolower($type), 'delete') ? '삭제' : '수정' }}할 수 있습니다.
            @endif
        </div>
        </div>
        <div class="panel-body row">
            <form class="contents col-sm-10 col-sm-offset-1" role="form" method="POST" action="{{ route('board.password.compare') }}">
                {{ csrf_field() }}
                <input type="hidden" name="type" value="{{ $type }}">
                <input type="hidden" name="boardName" value="{{ $boardName }}">
                <input type="hidden" name="writeId" value="{{ $writeId }}">
                @if($commentId)
                    <input type="hidden" name="commentId" value="{{ $commentId }}">
                @endif

                <input type="hidden" name="nextUrl" value="{{ $nextUrl }}">
                <div class="form-group">
                    <label for="password" class="control-label">비밀번호</label>
                    <input id="password" type="password" class="form-control" name="password" required>
                </div>

                <div class="form-group">
                    <div>
                        <button type="submit" class="btn btn-sir">확인</button>
                        <button type="button" class="btn btn-sir" onclick="history.back();">돌아가기</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>
@endsection
