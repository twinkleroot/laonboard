<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $user->nick }}님의 스크랩 | {{ Cache::get("config.homepage")->title }}</title>

    <!-- css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('font-awesome/css/font-awesome.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/common.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/scrap.css') }}">

    <!-- js -->
    <script src="{{ asset('js/common.js') }}"></script>
</head>
<body>

<div id="header">
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
    <form>
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
                        <a herf="/bbs/{{ $scrap->table_name }}/view/{{ $scrap->write_id }}" target="_blank" onclick="opener.document.location.href='/bbs/{{ $scrap->table_name }}/view/{{ $scrap->write_id }}'; return false;">{{ $scrap->write_subject }}</a>
                    @endif
                </td>

                <td class="td_datetime">{{ $scrap->created_at }}</td>
                <td class="td_mng">
                    <a href="{{ route('scrap.destroy', $scrap->id) }}" onclick="del(this.href); return false;">삭제</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">자료가 없습니다.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </form>
</div>

{{ $scraps->links() }}

</body>
</html>
