@extends('themes.default.basic')

@section('title')
    게시판 관리 | {{ $config->title }}
@endsection

@section('include_script')
    <script src="{{ asset('js/common.js') }}"></script>
@endsection

@section('content')
@if(Session::has('message'))
  <div class="alert alert-info">
    {{ Session::get('message') }}
  </div>
@endif
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">게시판 관리</div>
            <div class="panel-heading">
                <a href="{{ route('admin.boards.index') }}" >전체목록</a> | 생성된 게시판수 {{ $boards->total() }}개
            </div>
            <form class="form-horizontal" role="form" method="GET" action="{{ route('admin.search') }}">
                <input type="hidden" name="admin_page" value="board" />
                 <p>
                    <select name="kind">
                        <option value="table_name" @if($kind == 'table_name') selected @endif>TABLE</option>
                        <option value="subject" @if($kind == 'subject') selected @endif>제목</option>
                        <option value="group_id" @if($kind == 'group_id') selected @endif>그룹ID</option>
                    </select>
                    <input type="text" name="keyword" value="{{ $keyword }}" />
                    <input type="submit" class="btn btn-primary" value="검색" />
                </p>
            </form>
            <div class="panel-heading"><a class="btn btn-primary" href={{ route('admin.boards.create')}}>게시판 추가</a></div>
            <form class="form-horizontal" role="form" method="POST" id="selectForm" action="">
                <input type="hidden" id='ids' name='ids' value='' />
                <input type="hidden" id='group_ids' name='group_ids' value='' />
                <input type="hidden" id='subjects' name='subjects' value='' />
                <input type="hidden" id='read_points' name='read_points' value='' />
                <input type="hidden" id='write_points' name='write_points' value='' />
                <input type="hidden" id='comment_points' name='comment_points' value='' />
                <input type="hidden" id='download_points' name='download_points' value='' />
                <input type="hidden" id='use_snss' name='use_snss' value='' />
                <input type="hidden" id='use_searchs' name='use_searchs' value='' />
                <input type="hidden" id='orders' name='orders' value='' />
                <input type="hidden" id='devices' name='devices' value='' />
                <input type="hidden" id='_method' name='_method' value='' />
                <div class="panel-body">
                    {{ csrf_field() }}
                    <table class="table table-hover">
                        <thead>
                            <th class="text-center"><input type="checkbox" name="chkAll" onclick="checkAll(this.form)"/></th>
                            <th class="text-center">그룹</th>
                            <th class="text-center">TABLE</th>
                            <th class="text-center">스킨</th>
                            <th class="text-center">모바일<br />스킨</th>
                            <th class="text-center">제목</th>
                            <th class="text-center">읽기P</th>
                            <th class="text-center">쓰기P</th>
                            <th class="text-center">댓글P</th>
                            <th class="text-center">다운P</th>
                            <th class="text-center">SNS<br />사용</th>
                            <th class="text-center">검색<br />사용</th>
                            <th class="text-center">출력<br />순서</th>
                            <th class="text-center">접속기기</th>
                            <th class="text-center">관리</th>
                        </thead>

                        <tbody>
                        @if(count($boards) > 0)
                        @foreach ($boards as $board)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="chkId[]" class="boardId" value='{{ $board->id }}' /></td>
                                <td class="text-center">
                                    <select id="group_id_{{ $board->id }}">
                                    @foreach ($groups as $group)
                                        <option @if($board->group_id == $group->id) selected @endif value="{{ $group->id }}">
                                            {{ $group->subject }}
                                        </option>
                                    @endforeach
                                    </select>
                                </td>
                                <td class="text-center"><a href="{{ route('board.index', $board->id) }}">{{ $board->table_name }}</a></td>
                                <td class="text-center">{{ $board->skin }}</td>
                                <td class="text-center">{{ $board->mobile_skin }}</td>
                                <td class="text-center">
                                    <input type="text" id="subject_{{ $board->id }}" value="{{ $board->subject }}" />
                                </td>
                                <td class="text-center">
                                    <input type="text" id="read_point_{{ $board->id }}" value="{{ $board->read_point }}" />
                                </td>
                                <td class="text-center">
                                    <input type="text" id="write_point_{{ $board->id }}" value="{{ $board->write_point }}" />
                                </td>
                                <td class="text-center">
                                    <input type="text" id="comment_point_{{ $board->id }}" value="{{ $board->comment_point }}" />
                                </td>
                                <td class="text-center">
                                    <input type="text" id="download_point_{{ $board->id }}" value="{{ $board->download_point }}" />
                                </td>
                                <td class="text-center">
                                    <input type='checkbox' id='use_sns_{{ $board->id }}' value='1'
                                        {{ ($board->use_sns == '1' ? 'checked' : '') }}/>
                                </td>
                                <td class="text-center">
                                    <input type='checkbox' id='use_search_{{ $board->id }}' value='1'
                                        {{ ($board->use_search == '1' ? 'checked' : '') }}/>
                                </td>
                                <td class="text-center">
                                    <input type="text" id="order_{{ $board->id }}" value="{{ $board->order }}" />
                                </td>
                                <td class="text-center">
                                    <select id='device_{{ $board->id }}'>
                                        <option value='both' {{ $board->device == 'both' ? 'selected' : '' }}>모두</option>
                                        <option value='pc' {{ $board->device == 'pc' ? 'selected' : '' }}>PC</option>
                                        <option value='mobile' {{ $board->device == 'mobile' ? 'selected' : '' }}>모바일</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.boards.edit', $board->id) }}">수정</a>
                                    <a href="{{ route('admin.boards.copyForm', $board->id) }}" class="board_copy" target="win_board_copy">
                                        복사
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        @else
                            <tr>
                                <td class="text-center" colspan="15">
                                    자료가 없습니다.
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="panel-heading">
                    <input type="button" id="selected_update" class="btn btn-primary" value="선택 수정"/>
                    <input type="button" id="selected_delete" class="btn btn-primary" value="선택 삭제"/>
                </div>
            </form>

            {{-- 페이지 처리 --}}
            {{ str_contains(url()->current(), 'search')
                ? $boards->appends([
                    'admin_page' => 'board',
                    'kind' => $kind,
                    'keyword' => $keyword,
                ])->links()
                : $boards->links()
            }}

        </div>
    </div>
</div>
<script>
$(function(){
    // 선택 삭제 버튼 클릭
    $('#selected_delete').click(function(){
        var selected_id_array = selectIdsByCheckBox(".boardId");

        if(selected_id_array.length == 0) {
            alert('게시판을 선택해 주세요.')
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

        var group_array = toUpdateBySelectOption("group_id", selected_id_array);
        var subject_array = toUpdateByText("subject", selected_id_array);
        var read_point_array = toUpdateByText("read_point", selected_id_array);
        var write_point_array = toUpdateByText("write_point", selected_id_array);
        var comment_point_array = toUpdateByText("comment_point", selected_id_array);
        var download_point_array = toUpdateByText("download_point", selected_id_array);
        var use_sns_array = toUpdateByCheckBox("use_sns", selected_id_array);
        var use_search_array = toUpdateByCheckBox("use_search", selected_id_array);
        var order_array = toUpdateByText("order", selected_id_array);
        var device_array = toUpdateBySelectOption("device", selected_id_array);

        $('#ids').val(selected_id_array);
        $('#group_ids').val(group_array);
        $('#subjects').val(subject_array);
        $('#read_points').val(read_point_array);
        $('#write_points').val(write_point_array);
        $('#comment_points').val(comment_point_array);
        $('#download_points').val(download_point_array);
        $('#use_snss').val(use_sns_array);
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

// 체크박스로 업데이트할 값 배열에 담기
function toUpdateByCheckBox(id, selected_id_array) {
    var send_array = Array();
    for(i=0; i<selected_id_array.length; i++) {
        var chkbox = $('input[id= ' + id + '_' + selected_id_array[i] + ']');
        if(chkbox.is(':checked')) {
            send_array[i] = chkbox.val();
        } else {
            send_array[i] = 0;
        }
    }

    return send_array;
}

// 셀렉트박스로 업데이트할 값 배열에 담기
function toUpdateBySelectOption(id, selected_id_array) {
    var send_array = Array();
    for(i=0; i<selected_id_array.length; i++) {
        send_array[i] = $('select[id=' + id + '_' + selected_id_array[i] + ']').val();
    }

    return send_array;
}

// 텍스트 입력으로 업데이트할 값 배열에 담기
function toUpdateByText(id, selected_id_array) {
    var send_array = Array();
    for(i=0; i<selected_id_array.length; i++) {
        send_array[i] = $('input[id=' + id + '_' + selected_id_array[i] + ']').val();
    }

    return send_array;
}
</script>
@endsection
