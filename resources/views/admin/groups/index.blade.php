@extends('theme')

@section('title')
    LaBoard | 게시판 그룹 설정
@endsection

@section('content')
@if(Session::has('message'))
  <div class="alert alert-info">
    {{ Session::get('message') }}
  </div>
@endif
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
            <div class="panel-heading">게시판 그룹 설정</div>
            <div class="panel-heading">
                <a href="{{ route('admin.groups.index') }}" >처음</a> | 전체그룹 {{ count($groups) }}개
            </div>
            <form method="GET" id="searchForm" action="/admin/search">
                <input type="hidden" id="admin_page" name="admin_page" value="boardGroup" />
                <div class="panel-heading">
                    <select name="kind" id="kind">
                        <option value="subject">제목</option>
                        <option value="group_id">ID</option>
                        <option value="admin">그룹관리자</option>
                    </select>
                    <input type="text" id="keyword" name="keyword" value="" />
                    <input type="submit" id="search" value="검색" />
                </div>
            </form>
            <div class="panel-heading"><a class="btn btn-primary" href={{ route('admin.groups.create')}}>게시판 그룹 추가</a></div>
            <form class="form-horizontal" role="form" method="POST" id="selectForm" action="">
                <input type="hidden" id='ids' name='ids' value='' />
                <input type="hidden" id='subjects' name='subjects' value='' />
                <input type="hidden" id='admins' name='admins' value='' />
                <input type="hidden" id='use_accesss' name='use_accesss' value='' />
                <input type="hidden" id='orders' name='orders' value='' />
                <input type="hidden" id='devices' name='devices' value='' />
                <input type="hidden" id='_method' name='_method' value='' />
                <div class="panel-body">
                    {{ csrf_field() }}
                    <table class="table table-hover">
                        <thead>
                            <th class="text-center"><input type="checkbox" name="chkAll" onclick="checkAll(this.form)"/></th>
                            <th class="text-center">그룹 ID</th>
                            <th class="text-center">제목</th>
                            <th class="text-center">그룹관리자</th>
                            <th class="text-center">게시판</th>
                            <th class="text-center">접근<br />사용</th>
                            <th class="text-center">접근<br />회원수</th>
                            <th class="text-center">출력순서</th>
                            <th class="text-center">접속기기</th>
                            <th class="text-center">관리</th>
                        </thead>

                        <tbody>
                        @foreach ($groups as $group)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="chk[]" class="groupId" value='{{ $group->id }}' /></td>
                                <td class="text-center">{{ $group->group_id }}</td>
                                <td class="text-center"><input type="text" id='subject_{{ $group->id }}' value='{{ $group->subject }}' /></td>
                                <td class="text-center"><input type="text" id='admin_{{ $group->id }}' value='{{ $group->admin }}' /></td>
                                <td class="text-center">?<!-- 게시판 수 --></td>
                                <td class="text-center">
                                    <input type='checkbox' id='use_access_{{ $group->id }}' value='1'
                                        {{ ($group->use_access == '1' ? 'checked' : '') }}/></td>
                                <td class="text-center">
                                    <a href="{{ route('admin.accessUsers.show', $group->id)}}">{{ $group->count_users }}</a></td>
                                <td class="text-center"><input type="text" id='order_{{ $group->id }}' value='{{ $group->order }}' /></td>
                                <td class="text-center">
                                    <select id='device_{{ $group->id }}'>
                                        <option value='both' {{ $group->device == 'both' ? 'selected' : '' }}>both</option>
                                        <option value='pc' {{ $group->device == 'pc' ? 'selected' : '' }}>pc</option>
                                        <option value='mobile' {{ $group->device == 'mobile' ? 'selected' : '' }}>mobile</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-primary" href="{{ route('admin.groups.edit', $group->id) }}">수정</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="panel-heading">
                    <input type="button" id="selected_update" class="btn btn-primary" value="선택 수정"/>
                    <input type="button" id="selected_delete" class="btn btn-primary" value="선택 삭제"/>
                    <a class="btn btn-primary" href={{ route('admin.groups.create')}}>게시판 그룹 추가</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
$(function(){
    // 선택 삭제 버튼 클릭
    $('#selected_delete').click(function(){
        var selected_id_array = selectIdsByCheckBox(".groupId");

        if(selected_id_array.length == 0) {
            alert('게시판 그룹을 선택해 주세요.')
            return;
        }

        $('#ids').val(selected_id_array);
        $('#_method').val('DELETE');
        <?php $ids=''; ?>
        $('#selectForm').attr('action', '{!! route('admin.groups.destroy', $ids) !!}' + '/' + selected_id_array);
        $('#selectForm').submit();
    });

    // 선택 수정 버튼 클릭
    $('#selected_update').click(function(){

        var selected_id_array = selectIdsByCheckBox(".groupId");

        if(selected_id_array.length == 0) {
            alert('게시판 그룹을 선택해 주세요.')
            return;
        }

        // 목록에서 제목, 그룹관리자, 접근사용, 출력순서, 접속기기 변경 가능
        var subject_array = toUpdateByInput("subject", selected_id_array);
        var admin_array = toUpdateByInput("admin", selected_id_array);
        var order_array = toUpdateByInput("order", selected_id_array);
        var use_access_array = toUpdateByCheckBox("use_access", selected_id_array);
        var device_array = toUpdateBySelectOption("device", selected_id_array);

        $('#ids').val(selected_id_array);
        $('#subjects').val(subject_array);
        $('#admins').val(admin_array);
        $('#orders').val(order_array);
        $('#use_accesss').val(use_access_array);
        $('#devices').val(device_array);
        $('#_method').val('PUT');
        $('#selectForm').attr('action', '{!! route('admin.groups.selectedUpdate') !!}');
        $('#selectForm').submit();
    });

});

// 체크박스로 id 선택
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
            send_array[i] = 0;
        }
    }

    return send_array;
}

function toUpdateByInput(id, selected_id_array) {
    var send_array = Array();
    for(i=0; i<selected_id_array.length; i++) {
        send_array[i] = $('input[id= ' + id + '_' + selected_id_array[i] + ']').val();
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
