@extends("themes.". cache('config.theme')->name. ".layouts.basic")

@section('title')새글 | {{ Cache::get("config.homepage")->title }}@endsection

@section('include_css')
<link rel="stylesheet" type="text/css" href="{{ ver_asset("themes/default/css/common.css") }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset("themes/default/css/new.css") }}">
@endsection

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
@endsection

@section('content')
<div id="board">
    <div class="container">
        <div class="bd_head">
            <span>새글</span>
        </div>
        <div class="bd_sch">
            <form method='get' action='{{ route('new.index') }}'>
                <label for="groupId" class="sr-only">그룹</label>
                <select name="groupId" id="groupId">
                    <option value="">전체그룹</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" @if($groupId == $group->id) selected @endif>{{ $group->subject }}</option>
                    @endforeach
                </select>

                <label for="type" class="sr-only">검색대상</label>
                <select name="type" id="type">
                    <option value="">전체게시물</option>
                    <option value="w" @if($type == 'w') selected @endif>원글만</option>
                    <option value="c" @if($type == 'c') selected @endif>코멘트만</option>
                </select>

                <label for="nick" class="sr-only">검색어</label>
                <input type="text" name="nick" value="{{ $nick }}" id="nick" class="search" required>
                <button type="submit" id="" class="search-icon">
                    <i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">검색</span>
                </button>
            </form>
        </div>
        <div class="bd_new">회원의 닉네임으로만 검색 가능(비회원글 검색 불가)</div>

        <form id="listForm" method="post" action="{{ route('new.destroy') }}">
            {{ csrf_field()}}
        <table class="table box">
            <thead>
                <tr>
                    @if(session()->get('admin'))
                    <th class="mo"> <!-- 전체선택 -->
                        <input type="checkbox" name="chkAll" onclick="checkAll(this.form)">
                    </th>
                    @endif
                    <th class="mo">그룹</th>
                    <th class="mo">게시판</th>
                    <th>제목</th>
                    <th class="mo">이름</th>
                    <th>일시</th>
                </tr>
            </thead>
            <tbody>
                @forelse($boardNewList as $boardNew)
                <tr>
                    @if(session()->get('admin'))
                    <td class="bd_check mo"><input type="checkbox" name="chkId[]" class="newId" value='{{ $boardNew->id }}'></td>
                    @endif
                    <td class="td_board mo">
                        <a href="{{ route('new.index') }}?groupId={{ $boardNew->group_id }}">{{ $boardNew->group_subject }}</a>
                    </td>
                    <td class="td_board mo">
                        <a href="{{ route('board.index', $boardNew->table_name) }}">{{ $boardNew->subject }}</a>
                    </td>
                    <td>
                        <span class="bd_subject"><a href="/bbs/{{ $boardNew->table_name}}/views/{{ $boardNew->write_parent. $boardNew->commentTag }}">{{ $boardNew->writeSubject }}</a></span>
                    </td>
                    <td class="td_nick mo">
                    @unless($boardNew->user_id)
                        {{ $boardNew->name }}
                    @else
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                            @if(cache('config.join')->useMemberIcon && $boardNew->write->iconPath)
                            <span class="tt_icon"><img src="{{ $boardNew->write->iconPath }}" /></span> <!-- 아이콘 -->
                            @endif
                            <span class="tt_nick">{{ $boardNew->name }}</span> <!-- 닉네임 -->
                        </a>
                        @component(getFrontSideview(), ['sideview' => 'other', 'id' => $boardNew->user_id_hashkey, 'name' => $boardNew->name, 'email' => $boardNew->user_email])
                        @endcomponent
                    @endunless
                    </td>
                    <td class="td_date">@if($today->toDateString() == substr($boardNew->created_at, 0, 10)) @hourAndMin($boardNew->created_at) @else @monthAndDay($boardNew->created_at) @endif</td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ session()->get('admin') ? 6 : 5}}">
                        게시물이 없습니다.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if(session()->get('admin'))
        <div class="bd_btn">
            <button type="button" class="btn btn-sir" onclick="confirmDel()">선택삭제</button>
        </div>
        @endif
        </form>
    </div>
</div>

{{ $boardNewList->appends([
    'groupId' => $groupId,
    'type' => $type,
    'nick' => $nick,
])->links() }}

<script>
function confirmDel() {
    var selectedIdArray = selectIdsByCheckBox(".newId");

    if(selectedIdArray.length == 0) {
        alert('선택삭제할 게시물을 한 개 이상 선택하세요.')
        return false;
    }

    if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다.")) {
            return false;
    }
    $("#listForm").submit();
}

$(document).ready(function(){
    $('#groupId').change(function() {
        var gId = $('#groupId').val();
        if(gId) {
            location.href = '/news?groupId=' + gId;
        } else {
            location.href = '/news';
        }
    });
});
</script>
@endsection
