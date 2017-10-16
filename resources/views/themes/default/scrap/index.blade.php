<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $user->nick }}님의 스크랩 | {{ Cache::get("config.homepage")->title }}</title>
<!-- css -->
<link rel="stylesheet" type="text/css" href="{{ ver_asset('bootstrap/css/bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('font-awesome/css/font-awesome.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/common.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('css/scrap.css') }}">
<!-- js -->
<script src="{{ ver_asset('js/common.js') }}"></script>
</head>
<body class="popup">

<div id="header" class="popup">
<div class="container">
    <div class="title">
        <span>{{ $user->nick }}님의 스크랩</span>
    </div>

    <div class="cbtn">
        <button class="btn btn-default" onclick="window.close()">창닫기</button>
    </div>
</div>
</div>

<div id="bd_scrap" class="container">
    <table class="table box">
        <thead>
            <tr>
                <th>번호</th>
                <th>게시판</th>
                <th>제목</th>
                <th>보관일시</th>
                <th>삭제</th>
            </tr>
        </thead>
        <tbody>
            @forelse($scraps as $scrap)
            <tr>
                <td class="td_num">{{ $scraps->total() - ($scraps->currentPage() - 1) * Cache::get('config.homepage')->pageRows - $loop->index }}</td>

                <td class="td_board">
                    @if(isset($scrap->board_empty) && $scrap->board_empty)
                        {{ $scrap->board_subject }}
                    @else
                        <a herf="/bbs/{{ $scrap->table_name }}" target="_blank" onclick="opener.document.location.href='/bbs/{{ $scrap->table_name }}'; return false;">{{ $scrap->board_subject }}</a>
                    @endif
                </td>

                <td>
                    @if(isset($scrap->write_empty) && $scrap->write_empty)
                        {{ $scrap->write_subject }}
                    @else
                        <a herf="/bbs/{{ $scrap->table_name }}/views/{{ $scrap->write_id }}" target="_blank" onclick="opener.document.location.href='/bbs/{{ $scrap->table_name }}/views/{{ $scrap->write_id }}'; return false;">{{ $scrap->write_subject }}</a>
                    @endif
                </td>

                <td class="td_datetime">{{ $scrap->created_at }}</td>
                <td class="td_mng">
                    <a href="{{ route('scrap.destroy', $scrap->id) }}" onclick="delPost('deleteForm{{ $scrap->id }}')">
                        삭제
                    </a>
                    <form id="deleteForm{{ $scrap->id }}" action="{{ route('scrap.destroy', $scrap->id) }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">자료가 없습니다.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $scraps->links() }}

</body>
</html>
