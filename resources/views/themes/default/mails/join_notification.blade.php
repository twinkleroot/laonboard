<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title>회원가입 알림 메일</title>
</head>
<body style="padding: 0; margin: 0;">
    <div style="width: 600px; border: 1px solid #d8d8d8; border-radius: 4px; padding: 0 50px; font-family: '맑은 고딕', 'Malgun Gothic', sans-serif;">
        <h1 style="margin: 40px 0 20px; font-weight: normal; font-size: 20px;">
            <a href="/" target="_blank">{{ Cache::get('config.homepage')->title }}</a> 회원가입 알림 메일입니다.
        </h1>
        <hr style="width: 100%; height: 0; border-top: 2px solid #587ef6; margin: 20px 0;">
        <div style="margin: 20px 0 40px; font-size: 14px; line-height: 1.8;">
            <b>{{ $user->nick }}</b>님께서 회원가입 하셨습니다.

            <div style="background: #eee; padding: 20px; margin: 20px 0; border-radius: 4px;">
                회원 이메일 : <b>{{ $user->email }}</b><br>
                회원 닉네임 : {{ $user->nick }}<br>
            </div>

            <div style="background: #587ef6; width: 100%; border-radius: 4px; text-align: center; margin: 20px 0;">
                <a href="{{ route('admin.users.edit', $user->id) }}" target="_blank" style="display: block; padding: 25px 0; color: #fff; text-decoration: none;">관리자에서 회원정보 확인하기</a>
            </div>
        </div>
    </div>
</body>
</html>
