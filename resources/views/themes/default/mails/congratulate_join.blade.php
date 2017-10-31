<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title>회원가입 축하 메일</title>
</head>
<body style="padding: 0; margin: 0;">
    <div style="width: 600px; border: 1px solid #d8d8d8; border-radius: 4px; padding: 0 50px; font-family: '맑은 고딕', 'Malgun Gothic', sans-serif;">
        <h1 style="margin: 40px 0 20px; font-weight: normal; font-size: 20px;">
            <a href="/" target="_blank">{{ Cache::get('config.homepage')->title }}</a> 회원가입을 축하합니다.
        </h1>
        <hr style="width: 100%; height: 0; border-top: 2px solid #587ef6; margin: 20px 0;">
        <div style="margin: 20px 0 40px; font-size: 14px; line-height: 1.8;">
            <b>{{ $user->nick }}</b>님의 회원가입을 진심으로 축하합니다.<br>
            회원님의 성원에 보답하고자 더욱 더 열심히 하겠습니다.<br>
            @if(Cache::get('config.email.default')->emailCertify)
                아래의 <strong>메일인증</strong>을 클릭하시면 회원가입이 완료됩니다.<br>
            @endif
            감사합니다.<br><br>

            @if(Cache::get('config.email.default')->emailCertify)
                <div style="background: #587ef6; width: 100%; border-radius: 4px; text-align: center; margin: 20px 0;">
                    <a href="{{ $url }}" target="_blank" style="display: block; padding: 25px 0; color: #fff; text-decoration: none;">메일인증</a>
                </div>
            @else
                <div style="background: #587ef6; width: 100%; border-radius: 4px; text-align: center; margin: 20px 0;">
                    <a href="{{ route('home') }}" target="_blank" style="display: block; padding: 25px 0; color: #fff; text-decoration: none;">사이트 바로가기</a>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
