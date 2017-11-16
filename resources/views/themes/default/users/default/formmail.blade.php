<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>메일 쓰기</title>
<!-- css -->
<link rel="stylesheet" type="text/css" href="{{ ver_asset('bootstrap/css/bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('font-awesome/css/font-awesome.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/common.css') }}">
<!-- js -->
<script src="{{ ver_asset('js/jquery-3.1.1.min.js') }}"></script>
<script src="{{ ver_asset('js/common.js') }}"></script>
</head>
<body class="popup">
<form method="post" id="mailForm" action="{{ route('user.mail.send') }}" role="form" enctype="multipart/form-data">
    {{ csrf_field() }}
    <input type="hidden" name="toUser" value="{{ $id }}" />
    @auth
    <input type="hidden" name="name" value="{{ auth()->user()->nick }}" />
    <input type="hidden" name="email" value="{{ auth()->user()->email }}" />
    @endauth
<div id="header" class="popup">
    <div class="container">
        <div class="title" style="border-bottom: 0;">
            <span>[{{ $name }}]님께 메일보내기</span>
        </div>

        <div class="cbtn">
            @if(auth()->check() && auth()->user()->isSuperAdmin())
            <button type="submit" class="btn btn-sir">메일발송</button>
            @else
            <button type="button" class="btn btn-sir submitBtn">메일발송</button>
            @endif
            <button class="btn btn-default" onclick="window.close();">창닫기</button>
        </div>
    </div>
</div>

<div id="mail" class="container">
    <p class="sr-only">메일쓰기</p>
    <table class="table box">
        <tbody>
            @guest
            <tr>
                <td class="popin">이름</td>
                <td @if($errors->has('name')) class="has-error" @endif>
                    <input type="text" class="form-control" name="name">
                    @if($errors->has('name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="popin">이메일</td>
                <td @if($errors->has('email')) class="has-error" @endif>
                    <input type="text" class="form-control" name="email">
                    @if($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                    @endif
                </td>
            </tr>
            @endguest
            <tr>
                <td class="popin">제목</td>
                <td @if($errors->has('subject')) class="has-error" @endif>
                    <input type="text" class="form-control" name="subject">
                    @if($errors->has('subject'))
                    <span class="help-block">
                        <strong>{{ $errors->first('subject') }}</strong>
                    </span>
                    @endif
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
                <td @if($errors->has('content')) class="has-error" @endif>
                    <textarea class="form-control" name="content" rows="4"></textarea>
                    @if($errors->has('content'))
                    <span class="help-block">
                        <strong>{{ $errors->first('content') }}</strong>
                    </span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="popin">첨부 파일 1</td>
                <td>
                    <input type="file" name="file[]" id="file1" class="frm_input">
                    <span class="help-block">첨부 파일은 누락될 수 있으므로 메일을 보낸 후 파일이 첨부 되었는지 반드시 확인해 주시기 바랍니다.</span>
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

    {{ fireEvent('captchaPlace') }}
        
</div>
</form>
</body>
</html>
