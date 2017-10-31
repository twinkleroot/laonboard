<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>받은 쪽지 보기</title>
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
            <span>{{ $kind == 'recv' ? '받은' : '보낸' }} 쪽지 보기</span>
        </div>

        <div class="cbtn">
            @if($prevMemo)
                <a class="btn btn-default" href="{{ route('memo.show', $prevMemo) }}?kind={{ $kind }}">이전쪽지</a>
            @endif
            @if($nextMemo)
                <a class="btn btn-default" href="{{ route('memo.show', $nextMemo) }}?kind={{ $kind }}">다음쪽지</a>
            @endif
            @if($kind!='send')
                <a class="btn btn-default" href="{{ route('memo.create', $memo->user_id_hashkey) }}?id={{ $memo->id }}">답장</a>
            @endif
            <a class="btn btn-default" href="{{ route('memo.index') }}?kind={{ $kind }}">목록보기</a>
            <button class="btn btn-default" onclick="window.close();">창닫기</button>
        </div>
    </div>
    <div class="header_ctg">
        <ul class="container">
            <li @if($kind == 'recv')class="on"@endif><a href="{{ route('memo.index') }}?kind=recv">받은쪽지</a></li>
            <li @if($kind == 'send')class="on"@endif><a href="{{ route('memo.index') }}?kind=send">보낸쪽지</a></li>
            <li><a href="{{ route('memo.create') }}">쪽지쓰기</a></li>
        </ul>
    </div>
</div>

<div id="memo" class="container">
    <article>
        <ul id="memo_view_li">
            <li class="info">
                <span class="memo_view_subj">보낸사람</span>
                <span class="bd_name">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">{{ $memo->nick }}</a>
                    @component(getFrontSideview(), ['sideview' => 'other', 'id' => $memo->user_id_hashkey, 'name' => $memo->nick, 'email' => $memo->email])
                    @endcomponent
                </span>
            </li>
            <li class="info">
                <span class="memo_view_subj">받은시간</span>
                <strong>{{ $memo->send_timestamp }}</strong>
            </li>
        </ul>
        {!! clean($memo->memo) !!}
    </article>
</div>
<!-- Placed at the end of the document so the pages load faster -->
<script src="../../themes/default/js/bootstrap.min.js"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="../../themes/default/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>
