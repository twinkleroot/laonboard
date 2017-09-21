<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>게시판 복사 | {{ Cache::get("config.homepage")->title }} </title>
<!-- css -->
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/bootstrap/bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('font-awesome/css/font-awesome.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('css/admin.css') }}">
<!-- Scripts -->
<script>
    window.Laravel = {!! json_encode([
        'csrfToken' => csrf_token(),
    ]) !!};
</script>
<script src="{{ ver_asset('js/jquery-3.1.1.min.js') }}"></script>
</head>

<body class="popup">
<form method="POST" action="{{ route('admin.boards.copy') }}">
    {{ csrf_field() }}
    <input type="hidden" name="id" value="{{ $board->id }}" />
    <div id="header" class="container">
        <div class="title">
            <span>게시판 복사</span>
        </div>
        <div class="cbtn">
            <input type="submit" class="btn btn-sir" value="복사">
            <input type="button" class="btn btn-default" onclick="window.close();" value="창닫기">
        </div>
    </div>

    @if(Session::has('message'))
        <div class="alert alert-info">
            {{ Session::get('message') }}
        </div>
    @endif

    <div id="board_copy" class="container">
        <div class="form-horizontal">
            <div class="form-group">
                <label for="" class="col-sm-2 col-xs-3 control-label">원본 테이블명</label>
                <div class="col-sm-3 col-xs-4">
                    {{ $board->table_name }}
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 col-xs-3 control-label">복사 테이블명</label>
                <div class="col-sm-10 col-xs-9">
                    <input type="text" name="table_name" class="form-control" value="" required maxlength="20"/>
                    <span class="help-block">영문자, 숫자, _만 가능(공백없이)</span>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 col-xs-3 control-label">게시판 제목</label>
                <div class="col-sm-10 col-xs-9">
                    <input type="text" name="subject" class="form-control" value="{{ '[복사본] ' . $board->subject }}" required/>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 col-xs-3 control-label">복사유형</label>
                <div class="col-sm-10 col-xs-9">
                    <input type="radio" name="copy_case" value="schema_only" id="copy_case" checked>
                    <label for="copy_case">구조만</label>
                    <input type="radio" name="copy_case" value="schema_data_both" id="copy_case2">
                    <label for="copy_case2">구조와 데이터</label>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
$(function(){
    @if($errors->any())
        alert('{{ $errors->first() }}');
    @endif
});
</script>
</body>
