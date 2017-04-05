@extends('theme')

@section('title')
    LaBoard | 게시판 관리
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
                <a href="{{ route('admin.boards.index') }}" >전체목록</a> | 생성된 게시판수 {{ count($boards) }}개
            </div>
            <form class="form-horizontal" role="form" method="GET" action="{{ route('admin.search') }}">
                <input type="hidden" name="admin_page" value="board" />
                 <p>
                    <select name="kind">
                        <option value="table">TABLE</option>
                        <option value="subject">제목</option>
                        <option value="group_id">그룹ID</option>
                    </select>
                    <input type="text" name="keyword" value="" />
                    <input type="submit" class="btn btn-primary" value="검색" />
                </p>
            </form>
            <div class="panel-heading"><a class="btn btn-primary" href={{ route('admin.boards.create')}}>게시판 추가</a></div>
            <form class="form-horizontal" role="form" method="POST" id="selectForm" action="">
                <input type="hidden" id='ids' name='ids' value='' />
                {{-- <input type="hidden" id='opens' name='opens' value='' />
                <input type="hidden" id='mailings' name='mailings' value='' />
                <input type="hidden" id='smss' name='smss' value='' />
                <input type="hidden" id='intercepts' name='intercepts' value='' />
                <input type="hidden" id='levels' name='levels' value='' /> --}}
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
                            <th class="text-center">출력<br />/순서</th>
                            <th class="text-center">접속기기</th>
                            <th class="text-center">관리</th>
                        </thead>

                        <tbody>
                        @if(count($boards) > 0)
                        @foreach ($boards as $board)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="chk[]" class="boardId" value='{{ $board->id }}' /></td>
                                <td class="text-center">
                                    <select>
                                    @foreach ($accessible_groups as $group)
                                        <option @if($board->group_id == $group) selected @endif value="{{ $group->id }}">
                                            {{ $group->subject }}
                                        </option>
                                    @endforeach
                                    </select>
                                </td>
                                <td class="text-center">{{ $board->table }}</td>
                                <td class="text-center">{{ $board->skin }}</td>
                                <td class="text-center">{{ $board->mobile_skin }}</td>
                                <td class="text-center">
                                    <input type="text" name="subject" value="{{ $board->subject }}" />
                                </td>
                                <td class="text-center">
                                    <input type="text" name="read_point" value="{{ $board->read_point }}" />
                                </td>
                                <td class="text-center">
                                    <input type="text" name="write_point" value="{{ $board->write_point }}" />
                                </td>
                                <td class="text-center">
                                    <input type="text" name="comment_point" value="{{ $board->comment_point }}" />
                                </td>
                                <td class="text-center">
                                    <input type="text" name="download_point" value="{{ $board->download_point }}" />
                                </td>
                                <td class="text-center">
                                    <input type='checkbox' id='sns_{{ $board->id }}' value='1'
                                        {{ ($board->use_sns == '1' ? 'checked' : '') }}/>
                                </td>
                                <td class="text-center">
                                    <input type='checkbox' id='search_{{ $board->id }}' value='1'
                                        {{ ($board->use_search == '1' ? 'checked' : '') }}/>
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
                                    <a href="{{ }}">복사</a>
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
            alert('게시판을 선택해 주세요.')
            return;
        }

        // var open_array = toUpdateByCheckBox("open", selected_id_array);
        // var mailing_array = toUpdateByCheckBox("mailing", selected_id_array);
        // var sms_array = toUpdateByCheckBox("sms", selected_id_array);
        // var intercept_array = toUpdateByCheckBox("intercept_date", selected_id_array);
        // var level_array = toUpdateBySelectOption("level", selected_id_array);

        $('#ids').val(selected_id_array);
        // $('#opens').val(open_array);
        // $('#mailings').val(mailing_array);
        // $('#smss').val(sms_array);
        // $('#intercepts').val(intercept_array);
        // $('#levels').val(level_array);
        $('#_method').val('PUT');
        $('#selectForm').attr('action', '{!! route('admin.boards.selectedUpdate') !!}');
        $('#selectForm').submit();
    });

});

function selectIdsByCheckBox(className) {
    var send_array = Array();
    var send_cnt = 0;
    var chkbox = $(className);

    for(i=0; i<chkbox.length; i++) {
        if(chkbox[i].checked == true) {
            send_array[send_cnt] = chkbox[i].value;
            send_cnt++;
        }
    }

    return send_array;
}

function toUpdateByCheckBox(id, selected_id_array) {
    var send_array = Array();
    for(i=0; i<selected_id_array.length; i++) {
        var chkbox = $('input[id= ' + id + '_' + selected_id_array[i] + ']');
        if(chkbox.is(':checked')) {
            send_array[i] = chkbox.val();
        } else {
            send_array[i] = 0;\

        }
    }

    return send_array;
}

function toUpdateBySelectOption(id, selected_id_array) {
    var send_array = Array();
    for(i=0; i<selected_id_array.length; i++) {
        send_array[i] = $('select[id=' + id + '_' + selected_id_array[i] + ']').val();
    }

    return send_array;
}

function checkAll(form) {
    var chk = document.getElementsByName("chk[]");

    for (i=0; i<chk.length; i++) {
        chk[i].checked = form.chkAll.checked;
    }
}
</script>
@endsection
