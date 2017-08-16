<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>쪽지 보내기</title>

    <!-- css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('font-awesome/css/font-awesome.css') }}">

    <!-- js -->
    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('js/common.js') }}"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="white">

<div id="header">
    <div class="container">
        <div class="title" style="border-bottom: 0;">
            <span>쪽지 보내기</span>
        </div>

        <div class="cbtn">
            <button class="btn btn-sir" onclick="validate();">보내기</button>
            <button class="btn btn-default" onclick="window.close();">창닫기</button>
        </div>
    </div>
    <div class="header_ctg">
        <ul class="container">
            <li><a href="{{ route('memo.index') }}?kind=recv">받은쪽지</a></li>
            <li><a href="{{ route('memo.index') }}?kind=send">보낸쪽지</a></li>
            <li><a href="{{ route('memo.create') }}">쪽지쓰기</a></li>
        </ul>
    </div>
</div>

<div id="memo" class="container">
    <p><strong>쪽지쓰기</strong></p>
    <form id="memoForm" method="post" action="{{ route('memo.store') }}" autocomplete="off">
        {{ csrf_field() }}
    <table class="table box">
        <tbody>
            <tr>
                <td class="popin">
                    받는 회원 닉네임
                </td>
                <td>
                    <input type="text" class="form-control" name="recv_nicks" size="47" @if( isset($to) ) value="{{ $to }}" @else value="{{ old('recv_nicks') }}" @endif required>
                    <p class="help-block">여러 회원에게 보낼때는 컴마(,)로 구분하세요.</p>
                </td>
            </tr>
            <tr>
                <td class="popin">
                    내용
                </td>
                <td>
                    <textarea class="form-control" rows="4" name="memo" id="memo" required>@if( isset($content) ) {{ $content }} @else {{ old('memo') }} @endif</textarea>
                </td>
            </tr>
        </tbody>
    </table>
    <!-- 리캡챠 -->
    <div id='recaptcha' class="g-recaptcha"
        data-sitekey="{{ cache('config.sns')->googleRecaptchaClient }}"
        data-callback="onSubmit"
        data-size="invisible" style="display:none">
    </div>
    </form>
</div>

<script>
function onSubmit(token) {
    $("#memoForm").submit();
}
function validate(event) {
    grecaptcha.execute();
}
</script>

</body>
</html>
