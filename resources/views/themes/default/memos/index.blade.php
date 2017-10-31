<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>내 쪽지함</title>
<!-- css -->
<link rel="stylesheet" type="text/css" href="{{ ver_asset('bootstrap/css/bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('font-awesome/css/font-awesome.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/common.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/memo.css') }}">
<!-- js -->
<script src="{{ ver_asset('js/jquery-3.1.1.min.js') }}"></script>
<script src="{{ ver_asset('js/common.js') }}"></script>
</head>
<body class="popup">

<div id="header" class="popup">
    <div class="container">
        <div class="title" style="border-bottom: 0;">
            <span>{{ $kind == 'send' ? '보낸' : '받은' }} 쪽지함</span>
        </div>

        <div class="cbtn">
            <button class="btn btn-default" onclick="window.close();">창닫기</button>
        </div>
    </div>
    <div class="header_ctg">
        <ul class="container">
            <li @if($kind == 'recv')class="on"@endif><a href="{{ route('memo.index') }}?kind=recv">받은쪽지</a></li>
            <li @if($kind == 'send')class="on"@endif><a href="{{ route('memo.index') }}?kind=send">보낸쪽지</a></li>
            <li><a href="{{ route('memo.create') }}?to=">쪽지쓰기</a></li>
        </ul>
    </div>
</div>

<div id="memo" class="container">
    <table class="table box">
        <thead>
            <tr>
                <th>{{ $kind == 'recv' ? '보낸' : '받는' }}사람</th>
                <th>보낸시간</th>
                <th>읽은시간</th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody>
            <!-- 하단 tr이 출력될 목록갯수에 따라 반복 -->
            @if($countMemo)
            @foreach($memos as $memo)
            <tr>
                <td class="td_nick">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">{{ $memo->nick }}</a>
                    @component(getFrontSideview(), ['sideview' => 'other', 'id' => $memo->user_id_hashkey, 'name' => $memo->nick, 'email' => $memo->email])
                    @endcomponent
                </td>
                <td class="td_datetime"><a href="{{ route('memo.show', $memo->id) }}?kind={{ $kind }}">{{ $memo->send_timestamp }}</a></td>
                <td class="td_datetime"><a href="{{ route('memo.show', $memo->id) }}?kind={{ $kind }}">{{ $memo->read_timestamp ? : '아직 읽지 않음' }}</a></td>
                <td class="td_mngsmall">
                    <a href="{{ route('memo.destroy', $memo->id) }}" onclick="delPost('deleteForm{{ $memo->id }}')">삭제</a>
                    <form id="deleteForm{{ $memo->id }}" action="{{ route('memo.destroy', $memo->id) }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <input type="hidden" name="kind" value="{{ $kind }}" />
                    </form>
                </td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="4">
                    <div class="empty_table">
                        <span>자료가 없습니다.</span>
                    </div>
                </td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="help bg-info">
        쪽지 보관일수는 최장 <strong>{{ cache('config.homepage')->memoDel }}일</strong> 입니다.
    </div>
</div>
<!-- Placed at the end of the document so the pages load faster -->
<script src="../../themes/default/js/bootstrap.min.js"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="../../themes/default/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>
