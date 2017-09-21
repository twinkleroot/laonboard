<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title>{{ $subject }} 메일</title>
</head>
<body style="padding: 0; margin: 0;">
    <div style="width: 600px; border: 1px solid #d8d8d8; border-radius: 4px; padding: 0 50px; font-family: '맑은 고딕', 'Malgun Gothic', sans-serif;">
        <h1 style="margin: 40px 0 20px; font-weight: normal; font-size: 20px;">
            {{ $subject }}
        </h1>
        <hr style="width: 100%; height: 0; border-top: 2px solid #587ef6; margin: 20px 0;">
        <div style="margin: 20px 0 40px; font-size: 14px; line-height: 1.8;">
            <span style="display: block;">작성자 {{ $name }}</span>
            <div style="background: #eee; padding: 20px; margin: 20px 0; border-radius: 4px;">
                {!! $content !!}
            </div>

            <div style="background: #587ef6; width: 100%; border-radius: 4px; text-align: center; margin: 20px 0;">
                <a href="{{ $linkUrl }}" target="_blank" style="display: block; padding: 25px 0; color: #fff; text-decoration: none;">사이트에서 게시물 확인하기</a>
            </div>
        </div>
    </div>
</body>
</html>
