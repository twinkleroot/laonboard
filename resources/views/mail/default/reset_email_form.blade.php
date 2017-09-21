<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title>비밀번호 재설정 메일</title>
</head>
<body style="padding: 0; margin: 0;">
    <div style="width: 600px; border: 1px solid #d8d8d8; border-radius: 4px; padding: 0 50px; font-family: '맑은 고딕', 'Malgun Gothic', sans-serif;">
        <h1 style="margin: 40px 0 20px; font-weight: normal; font-size: 20px;">
            <a href="{{ env('APP_URL') }}" target="_blank">{{ Cache::get('config.homepage')->title }}</a> 비밀번호 재설정 메일입니다.
        </h1>
        <hr style="width: 100%; height: 0; border-top: 2px solid #587ef6; margin: 20px 0;">
        <div style="margin: 20px 0 40px; font-size: 14px; line-height: 1.8;">
            아래 주소를 클릭하시면 비밀번호 재설정 페이지로 연결됩니다.<br><br>
            <a href="{{ route('reset.create', $token) }}" target="_blank">{{ route('reset.create', $token) }}</a>
        </div>
    </div>
</body>
</html>
