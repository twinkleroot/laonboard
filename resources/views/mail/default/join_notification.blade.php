<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title>회원가입 알림 메일</title>
</head>

<body>

<div style="margin:30px auto;width:600px;border:10px solid #f7f7f7">
    <div style="border:1px solid #dedede">
        <h1 style="padding:30px 30px 0;background:#f7f7f7;color:#555;font-size:1.4em">
            회원가입 알림 메일
        </h1>
        <span style="display:block;padding:10px 30px 30px;background:#f7f7f7;text-align:right">
            <a href="/" target="_blank">{{ Cache::get('config.homepage')->title }}</a>
        </span>
        <p style="margin:20px 0 0;padding:30px 30px 50px;min-height:200px;height:auto !important;height:200px;border-bottom:1px solid #eee">
            <b>{{ $user->nick }}</b> 님께서 회원가입 하셨습니다.<br>
            회원 이메일 : <b>{{ $user->email }}</b><br>
            회원 닉네임 : {{ $user->nick }}<br>
        </p>
        <a href="{{ route('admin.users.edit', $user->id) }}" style="display:block;padding:30px 0;background:#484848;color:#fff;text-decoration:none;text-align:center">관리자에서 회원정보 확인하기</a>
    </div>
</div>

</body>
</html>
