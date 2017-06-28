<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>[{{ $user->nick }}]님의 프로필</title>

    <!-- css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('font-awesome/css/font-awesome.css') }}">

    <!-- js -->
</head>
<body class="white">

<div id="header">
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
				<td>@date($user->created_at) ({{ $diffDay }} 일)</td>
			</tr>
			<tr>
				<td class="subject">포인트</td>
				<td>{{ number_format($user->point) }} 점</td>
				<td class="subject">회원권한</td>
				<td>Lv. {{ $user->level }}</td>
			</tr>
			<tr>
				<td class="subject">최종접속일</td>
				<td colspan="3">{{ $user->today_login }}</td>
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