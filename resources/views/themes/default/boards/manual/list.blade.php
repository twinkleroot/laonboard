@section('fisrt_include_css')
<link rel="alternate" type="application/rss+xml" href="{{ url('rss') }}" title="RSS Feed {{ config('rss.title') }}">
@stop

<form name="fBoardList" id="fBoardList" action="" onsubmit="return formBoardListSubmit(this);" method="post" target="move">
    <input type="hidden" id='_method' name='_method' value='post' />
    <input type="hidden" id='type' name='type' value='' />
    <input type="hidden" id='page' name='page' value='{{ $writes->currentPage() }}' />
    {{ csrf_field() }}
    <div id="sub_menu">
        <a href="{{ route('board.index', $board->table_name) }}">
            <h1>{{ $board->subject }}</h1>
        </a>
        <ul class="post_list">
            @forelse($writes as $write)
                <li class="post @if(isset($request->writeId) && $request->writeId == $write->id)reading @endif">
                    <a href="/bbs/{{ $board->table_name }}/views/{{ $write->parent. (Request::getQueryString() ? '?'.Request::getQueryString() : '')}}" @if($write->reply != '')class="post_rpy" style="padding-left: calc(15px * {{ strlen($write->reply) }}" @endif>
                        {!! clean($write->subject) !!}
                    </a>
                </li>
            @empty
                게시물이 없습니다.
            @endforelse
        </ul>
    </div>
</form>

<script>
function searchFormSubmit(f) {
    if(f.keyword.value.trim() == '') {
        alert('검색어 : 필수 입력입니다.');
        return false;
    }
    return true;
}
// 관리자 메뉴 폼 서브밋 전 실행되는 함수
function formBoardListSubmit(f) {
    var selected_id_array = selectIdsByCheckBox(".writeId");
    if(selected_id_array.length == 0) {
        alert(document.pressed + '할 게시물을 한 개 이상 선택하세요.')
        return false;
    }
    if(document.pressed == "선택복사") {
        selectCopy("copy");
        return;
    }
    if(document.pressed == "선택이동") {
        selectCopy("move");
        return;
    }
    if(document.pressed == "선택삭제") {
        if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다\n\n답변글이 있는 게시글을 선택하신 경우\n답변글도 선택하셔야 게시글이 삭제됩니다.")) {
            return false;
        }
        f.removeAttribute("target");
        f.action = '/bbs/{{ $board->table_name }}/delete/ids/' + selected_id_array;
        f._method.value = 'DELETE';
    }
    return true;
}
// 선택한 게시물 복사 및 이동
function selectCopy(type) {
    var f = document.fBoardList;
    if (type == "copy") {
        str = "복사";
    } else {
        str = "이동";
    }
    var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");
    f.type.value = type;
    f.target = "move";
    f.action = "{{ route('board.list.move', $board->table_name)}}";
    f.submit();
}
</script>
