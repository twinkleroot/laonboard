<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>쪽지 보내기</title>
<!-- css -->
<link rel="stylesheet" type="text/css" href="{{ ver_asset('bootstrap/css/bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('font-awesome/css/font-awesome.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/common.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/memo.css') }}">
<!-- js -->
<script src="{{ ver_asset('js/jquery-3.1.1.min.js') }}"></script>
<script src="{{ ver_asset('js/common.js') }}"></script>
</head>
<body class="popup">
<form id="memoForm" method="post" action="{{ route('memo.store') }}" autocomplete="off">
{{ csrf_field() }}
<div id="header" class="popup">
    <div class="container">
        <div class="title" style="border-bottom: 0;">
            <span>쪽지 보내기</span>
        </div>

        <div class="cbtn">
            @if(auth()->check() && auth()->user()->isSuperAdmin())
            <button type="submit" class="btn btn-sir">보내기</button>
            @else
            <button type="button" class="btn btn-sir submitBtn">보내기</button>
            @endif
            <button type="button" class="btn btn-default" onclick="window.close();">창닫기</button>
        </div>
    </div>
    <div class="header_ctg">
        <ul class="container">
            <li><a href="{{ route('memo.index') }}?kind=recv">받은쪽지</a></li>
            <li><a href="{{ route('memo.index') }}?kind=send">보낸쪽지</a></li>
            <li class="on"><a href="{{ route('memo.create') }}">쪽지쓰기</a></li>
        </ul>
    </div>
</div>

<div id="memo" class="container">
    <p><strong>쪽지쓰기</strong></p>
    <table class="table box">
        <tbody>
            <tr>
                <td class="popin">
                    받는 회원 닉네임
                </td>
                <td @if($errors->has('name')) class="has-error" @endif>
                    <input type="text" class="form-control" name="recv_nicks" size="47" @if( isset($to) ) value="{{ $to }}" @else value="{{ old('recv_nicks') }}" @endif required>
                    <p class="help-block">여러 회원에게 보낼때는 컴마(,)로 구분하세요.</p>
                    @if($errors->has('recv_nicks'))
                    <span class="help-block">
                        <strong>{{ $errors->first('recv_nicks') }}</strong>
                    </span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="popin">
                    내용
                </td>
                <td @if($errors->has('name')) class="has-error" @endif>
                    <textarea class="form-control" rows="10" name="memo" id="memo" required>@if( isset($content) ){{ $content }}@else{{ old('memo') }}@endif</textarea>
                    @if($errors->has('memo'))
                    <span class="help-block">
                        <strong>{{ $errors->first('memo') }}</strong>
                    </span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    {{ fireEvent('captchaPlace') }}

    </form>
</div>

</body>
</html>
