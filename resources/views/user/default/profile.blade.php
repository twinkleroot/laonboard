<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>[{{ $user->nick }}]님의 프로필</title>
<!-- css -->
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/bootstrap/bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('font-awesome/css/font-awesome.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/common.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/profile.css') }}">
</head>
<body class="popup">
<div id="header" class="popup">
<div class="container">
    <div class="title">
        <span>[{{ $user->nick }}]님의 프로필</span>
    </div>

    <div class="cbtn">
        <button class="btn btn-sir" onclick="window.close();">창닫기</button>
    </div>
</div>
</div>

<div id="profile" class="container">
    <table class="table box">
        <tbody>
            <tr>
                <td class="subject">닉네임</td>
                <td>{{ $user->nick }}</td>
                <td class="subject">가입일</td>
                <td>@unless($user->isSuperAdmin())@date($user->created_at) ({{ $diffDay }} 일)@else 알 수 없음 @endunless</td>
            </tr>
            <tr>
                <td class="subject">포인트</td>
                <td>{{ number_format($user->point) }} 점</td>
                <td class="subject">회원권한</td>
                <td>Lv. {{ $user->level }}</td>
            </tr>
            <tr>
                <td class="subject">최종접속일</td>
                <td colspan="3">@unless($user->isSuperAdmin()){{ $user->today_login }}@else 알 수 없음 @endunless</td>
            </tr>
        </tbody>
    </table>

    <p class="profile_title"><strong>인사말</strong></p>
    <div class="introduce">
        <p>
            {{ $user->profile }}
        </p>
    </div>
</div>

</body>
</html>
