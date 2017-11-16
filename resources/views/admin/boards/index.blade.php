@extends('admin.layouts.basic')

@section('title')게시판 관리 | {{ Cache::get("config.homepage")->title }}@endsection

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>게시판 관리</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">게시판 관리</li>
            <li class="depth">게시판 관리</li>
        </ul>
    </div>
</div>
<div id="body_tab_type2">
    <span class="txt">생성된 게시판수 {{ $boards->total() }}개</span>
    <div class="submit_btn">
        @unless(isDemo())
        <a class="btn btn-default" href="{{ route('admin.boards.create') }}" role="button">게시판 추가</a>
        @endunless
    </div>
</div>

<div class="body-contents">
@if(Session::has('message'))
    <div id="adm_save">
        <span class="adm_save_txt">{{ Session::get('message') }}</span>
        <button onclick="alertclose()" class="adm_alert_close">
            <i class="fa fa-times"></i>
        </button>
    </div>
@endif
    <div id="board">
        <ul id="adm_btn">
            <li><a href="{{ route('admin.boards.index') }}" class="btn btn-sir" role="button">전체목록</a></li>
            <li><input type="button" id="selected_update" class="btn btn-sir" value="선택수정"/></li>
            <li><input type="button" id="selected_delete" class="btn btn-sir" value="선택삭제"/></li>
        </ul>
        <div id="adm_sch">
             <form role="form" method="GET" action="{{ route('admin.boards.index') }}">
                <label for="kind" class="sr-only">검색대상</label>
                <select name="kind">
                    <option value="table_name" @if($kind == 'table_name') selected @endif>TABLE</option>
                    <option value="subject" @if($kind == 'subject') selected @endif>제목</option>
                    <option value="group_id" @if($kind == 'group_id') selected @endif>그룹ID</option>
                </select>
                <label for="keyword" class="sr-only">검색어</label>
                <input type="text" name="keyword" class="search" value="{{ $keyword }}" />
                <button type="submit" id="" class="btn search-icon">
                    <i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">검색</span>
                </button>
            </form>
        </div>

        <form class="form-horizontal" role="form" method="POST" id="selectForm" action="">
            <input type="hidden" id='ids' name='ids' value='' />
            <input type="hidden" id='group_ids' name='group_ids' value='' />
            <input type="hidden" id='skin_ids' name='skin_ids' value='' />
            {{-- <input type="hidden" id='mobile_skin_ids' name='mobile_skin_ids' value='' /> --}}
            <input type="hidden" id='subjects' name='subjects' value='' />
            <input type="hidden" id='read_points' name='read_points' value='' />
            <input type="hidden" id='write_points' name='write_points' value='' />
            <input type="hidden" id='comment_points' name='comment_points' value='' />
            <input type="hidden" id='download_points' name='download_points' value='' />
            <input type="hidden" id='use_searchs' name='use_searchs' value='' />
            <input type="hidden" id='orders' name='orders' value='' />
            <input type="hidden" id='devices' name='devices' value='' />
            <input type="hidden" id='_method' name='_method' value='' />
            {{ csrf_field() }}
                <table class="table table-striped box">
                    <thead>
                        <tr>
                            <th><input type="checkbox" name="chkAll" onclick="checkAll(this.form)"/></th>
                            <th>
                                <a class="adm_sort" href="{{ route('admin.boards.index'). $queryString }}&amp;order=group_id&amp;direction={{$order=='group_id' ? $direction : 'asc'}}">그룹</a>
                            </th>
                            <th>
                                <a class="adm_sort" href="{{ route('admin.boards.index'). $queryString }}&amp;order=table_name&amp;direction={{$order=='table_name' ? $direction : 'asc'}}">TABLE</a>
                            </th>
                            <th>
                                <a class="adm_sort" href="{{ route('admin.boards.index'). $queryString }}&amp;order=skin&amp;direction={{$order=='skin' ? $direction : 'desc'}}">스킨</a>
                            </th>
                            {{-- <th class="text-center">
                                <a class="adm_sort" href="{{ route('admin.boards.index') }}?order=mobile_skin&amp;direction={{$order=='mobile_skin' ? $direction : 'desc'}}">모바일<br />스킨</a>
                            </th> --}}
                            <th>
                                <a class="adm_sort" href="{{ route('admin.boards.index'). $queryString }}&amp;order=subject&amp;direction={{$order=='subject' ? $direction : 'asc'}}">제목</a>
                            </th>
                            <th>읽기P</th>
                            <th>쓰기P</th>
                            <th>댓글P</th>
                            <th>다운P</th>
                            <th>
                                <a class="adm_sort" href="{{ route('admin.boards.index'). $queryString }}&amp;order=use_search&amp;direction={{$order=='use_search' ? $direction : 'asc'}}">검색<br />사용</a>
                            </th>
                            <th>
                                <a class="adm_sort" href="{{ route('admin.boards.index'). $queryString }}&amp;order=order&amp;direction={{$order=='order' ? $direction : 'asc'}}">출력<br />순서</a>
                            </th>
                            {{-- <th>접속기기</th> --}}
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($boards as $board)
                        <tr data-table="{{ $board->table_name }}">
                            <td class="td_chk">
                                <input type="checkbox" name="chkId[]" class="boardId" value='{{ $board->id }}' />
                            </td>
                            <td class="td_group">
                                <select id="group_id_{{ $board->id }}" class="form-control">
                                @foreach ($groups as $group)
                                    <option @if($board->group_id == $group->id) selected @endif value="{{ $group->id }}">
                                        {{ $group->subject }}
                                    </option>
                                @endforeach
                                </select>
                            </td>
                            <td class="td_mngsmall">
                                <a href="{{ route('board.index', $board->table_name) }}">{{ $board->table_name }}</a>
                            </td>
                            <td class="td_group">
                                <select id="skin_{{ $board->id }}" class="form-control">
                                @foreach ($skins as $skin)
                                    <option @if($board->skin == $skin) selected @endif value="{{ $skin }}">
                                        {{ $skin }}
                                    </option>
                                @endforeach
                                </select>
                            </td>
                            {{--<td class="td_group">
                                <select id="mobile_skin_{{ $board->id }}" class="form-control">
                                    @foreach ($mobileSkins as $skin)
                                        <option @if($board->mobile_skin == $skin) selected @endif value="{{ $skin }}">
                                            {{ $skin }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>--}}
                            <td>
                                <input type="text" class="form-control required" id="subject_{{ $board->id }}" value="{{ $board->subject }}">
                            </td>
                            <td class="td_numsmall">
                                <input type="text" class="form-control" id="read_point_{{ $board->id }}" value="{{ $board->read_point }}">
                            </td>
                            <td class="td_numsmall">
                                <input type="text" class="form-control" id="write_point_{{ $board->id }}" value="{{ $board->write_point }}">
                            </td>
                            <td class="td_numsmall">
                                <input type="text" class="form-control" id="comment_point_{{ $board->id }}" value="{{ $board->comment_point }}">
                            </td>
                            <td class="td_numsmall">
                                <input type="text" class="form-control" id="download_point_{{ $board->id }}" value="{{ $board->download_point }}">
                            </td>
                            <td class="td_numsmall">
                                <input type='checkbox' id='use_search_{{ $board->id }}' value='1' {{ ($board->use_search == '1' ? 'checked' : '') }}/>
                            </td>
                            <td class="td_numsmall">
                                <input type="text" class="form-control" id="order_{{ $board->id }}" value="{{ $board->order }}">
                            </td>
                            {{-- <td class="td_mngsmall">
                                <select id="device_{{ $board->id }}" class="form-control">
                                    <option value='both' {{ $board->device == 'both' ? 'selected' : '' }}>모두</option>
                                    <option value='pc' {{ $board->device == 'pc' ? 'selected' : '' }}>PC</option>
                                    <option value='mobile' {{ $board->device == 'mobile' ? 'selected' : '' }}>모바일</option>
                                </select>
                            </td> --}}
                            <td class="td_mngsmall">
                                <a href="{{ route('admin.boards.edit', $board->table_name). '?'. Request::getQueryString() }}">수정</a>
                                <a href="{{ route('admin.boards.copyForm', $board->table_name) }}" class="board_copy" target="win_board_copy">복사</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15">
                                <span class="empty_table">
                                    <i class="fa fa-exclamation-triangle"></i> 자료가 없습니다.
                                </span>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

            {{-- 페이지 처리 --}}
            {{ $boards->appends(Request::except('page'))->links() }}
        </form>
    </div>
</div>
<script>
var menuVal = 300100;
$(function(){
    // 선택 삭제 버튼 클릭
    $('#selected_delete').click(function(){
        var selected_id_array = selectIdsByCheckBox(".boardId");

        if(selected_id_array.length == 0) {
            alert('게시판을 선택해 주세요.')
            return;
        }

        if( !confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?")) {
            return;
        }

        $('#ids').val(selected_id_array);
        $('#_method').val('DELETE');
        $('#selectForm').attr('action', '/admin/boards/' + selected_id_array);
        $('#selectForm').submit();
    });

    // 선택 수정 버튼 클릭
    $('#selected_update').click(function(){

        var selected_id_array = selectIdsByCheckBox(".boardId");

        if(selected_id_array.length == 0) {
            alert('게시판을 선택해 주세요.');
            return;
        }

        if(!formValidate(selected_id_array)) {
            return false;
        }

        var group_array = toUpdateBySelectOption("group_id", selected_id_array);
        var skin_array = toUpdateBySelectOption("skin", selected_id_array);
        // var mobile_skin_array = toUpdateBySelectOption("mobile_skin", selected_id_array);
        var subject_array = toUpdateByText("subject", selected_id_array);
        var read_point_array = toUpdateByText("read_point", selected_id_array);
        var write_point_array = toUpdateByText("write_point", selected_id_array);
        var comment_point_array = toUpdateByText("comment_point", selected_id_array);
        var download_point_array = toUpdateByText("download_point", selected_id_array);
        var use_search_array = toUpdateByCheckBox("use_search", selected_id_array);
        var order_array = toUpdateByText("order", selected_id_array);
        var device_array = toUpdateBySelectOption("device", selected_id_array);

        $('#ids').val(selected_id_array);
        $('#group_ids').val(group_array);
        $('#skin_ids').val(skin_array);
        // $('#mobile_skin_ids').val(mobile_skin_array);
        $('#subjects').val(subject_array);
        $('#read_points').val(read_point_array);
        $('#write_points').val(write_point_array);
        $('#comment_points').val(comment_point_array);
        $('#download_points').val(download_point_array);
        $('#use_searchs').val(use_search_array);
        $('#orders').val(order_array);
        $('#devices').val(device_array);
        $('#_method').val('PUT');
        $('#selectForm').attr('action', '{!! route('admin.boards.selectedUpdate') !!}');
        $('#selectForm').submit();
    });

    // 복사 버튼 클릭
    $(".board_copy").click(function(){
        window.open(this.href, "win_board_copy", "left=100,top=100,width=550,height=450");
        return false;
    });
});

function formValidate(selected_id_array) {
    $("#adm_save").remove();
    $(".body-contents td").removeClass('has-error');

    var message = '';
    selected_id_array.forEach (function (v, i) {
        var table = $('input[id=subject_' + v + ']').closest('tr').attr('data-table');
        if($('input[id=subject_' + v + ']').val() == '') {
            $('input[id=subject_' + v + ']').closest('td').addClass('has-error');
            message += "<span class=\"adm_save_txt\">" + 'TABLE 이름이 ' + table + '인 게시판의 제목을 입력해 주세요.' + "</span><br>";
        }
        if(isNaN($('input[id=read_point_' + v + ']').val())) {
            $('input[id=read_point_' + v + ']').closest('td').addClass('has-error');
            message += "<span class=\"adm_save_txt\">" + 'TABLE 이름이 ' + table + '인 게시판의 읽기P에 숫자를 입력해 주세요.' + "</span><br>";
        }
        if(isNaN($('input[id=write_point_' + v + ']').val())) {
            $('input[id=write_point_' + v + ']').closest('td').addClass('has-error');
            message += "<span class=\"adm_save_txt\">" + 'TABLE 이름이 ' + table + '인 게시판의 쓰기P에 숫자를 입력해 주세요.' + "</span><br>";
        }
        if(isNaN($('input[id=comment_point_' + v + ']').val())) {
            $('input[id=comment_point_' + v + ']').closest('td').addClass('has-error');
            message += "<span class=\"adm_save_txt\">" + 'TABLE 이름이 ' + table + '인 게시판의 댓글P에 숫자를 입력해 주세요.' + "</span><br>";
        }
        if(isNaN($('input[id=download_point_' + v + ']').val())) {
            $('input[id=download_point_' + v + ']').closest('td').addClass('has-error');
            message += "<span class=\"adm_save_txt\">" + 'TABLE 이름이 ' + table + '인 게시판의 다운P에 숫자를 입력해 주세요.' + "</span><br>";
        }
        if(isNaN($('input[id=order_' + v + ']').val())) {
            $('input[id=order_' + v + ']').closest('td').addClass('has-error');
            message += "<span class=\"adm_save_txt\">" + 'TABLE 이름이 ' + table + '인 게시판의 출력순서에 숫자를 입력해 주세요.' + "</span><br>";
        }
    });

    if(message != '') {
        var htmlMessage = '';
        htmlMessage += "<div id=\"adm_save\">";
        htmlMessage += "<button onclick=\"alertclose()\" class=\"adm_alert_close\">";
        htmlMessage += "<i class=\"fa fa-times\"></i>";
        htmlMessage += "</button>";
        htmlMessage += message;
        htmlMessage += "</div>";
        $(".body-contents").prepend(htmlMessage);
        return false;
    }

    return true;
}

</script>
@endsection
