<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>메일 쓰기</title>

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
            <span>[{{ $name }}]님께 메일보내기</span>
        </div>

        <div class="cbtn">
            <button class="btn btn-sir" onclick="validate();">메일발송</button>
            <button class="btn btn-default" onclick="window.close();">창닫기</button>
        </div>
    </div>
</div>

<div id="mail" class="container">
    <p class="sr-only">메일쓰기</p>
    <form method="post" id="mailForm" action="{{ route('user.mail.send') }}" role="form" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" name="to" value="{{ $email }}" />
        @if(auth()->user())
        <input type="hidden" name="name" value="{{ auth()->user()->nick }}" />
        <input type="hidden" name="email" value="{{ auth()->user()->email }}" />
        @endif

    <table class="table box">
        <tbody>
            @if(auth()->guest())
            <tr>
                <td class="popin">이름</td>
                <td>
                    <input type="text" class="form-control" name="name">
                </td>
            </tr>
            <tr>
                <td class="popin">이메일</td>
                <td>
                    <input type="text" class="form-control" name="email">
                </td>
            </tr>
            @endif
            <tr>
                <td class="popin">제목</td>
                <td>
                    <input type="text" class="form-control" name="subject">
                </td>
            </tr>
            <tr>
                <td class="popin">형식</td>
                <td>
                    <input type="radio" id="type_text" name="type" value="0" checked><label for="type_text">TEXT</label>
                    <input type="radio" id="type_html" name="type" value="1"><label for="type_html">HTML</label>
                    <input type="radio" id="type_both" name="type" value="2"><label for="type_both">TEXT+HTML</label>
                </td>
            </tr>
            <tr>
                <td class="popin">내용</td>
                <td>
                    <textarea class="form-control" name="content" rows="4"></textarea>
                </td>
            </tr>
            <tr>
                <td class="popin">첨부 파일 1</td>
                <td>
                    <input type="file" name="file[]" id="file1" class="frm_input">
                    <p class="help-block">첨부 파일은 누락될 수 있으므로 메일을 보낸 후 파일이 첨부 되었는지 반드시 확인해 주시기 바랍니다.</p>
                </td>
            </tr>
            <tr>
                <td class="popin">첨부 파일 2</td>
                <td>
                    <input type="file" name="file[]" id="file2" class="frm_input">
                </td>
            </tr>
        </tbody>
    </table>

    <!-- 리캡챠 -->
    <div id='recaptcha' class="g-recaptcha"
        data-sitekey="{{ cache('config.sns')->googleRecaptcha }}"
        data-callback="onSubmit"
        data-size="invisible" style="display:none">
    </div>
    </form>
</div>

<script>
function onSubmit(token) {
    $("#mailForm").submit();
}
function validate(event) {
    grecaptcha.execute();
}
</script>
</body>
</html>
