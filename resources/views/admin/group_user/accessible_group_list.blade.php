@extends('theme')

@section('title')
    LaBoard | 접근 가능 그룹
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
            <div class="panel-heading"><h2>접근 가능 그룹</h2></div>
            이메일 <b>{{ $user->email }}</b>, 닉네임 <b>{{ $user->nick }}</b>@if(!is_null($user->name)), 이름 <b>{{ $user->name }}</b> @endif
            <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.accessGroups.store') }}">
                <input type="hidden" name="user_id" value="{{ $user->id }}" />
                {{ csrf_field() }}
                <p>
                    그룹지정
                    <select name="group_id">
                        <option>접근가능 그룹을 선택하세요.</option>
                        @foreach($accessible_groups as $accessible_group)
                            <option value="{{ $accessible_group->id }}">{{ $accessible_group->subject }}</option>
                        @endforeach
                    </select>
                    <input type="submit" class="btn btn-primary" value="선택" />
                </p>
            </form>
            <form class="form-horizontal" role="form" method="POST" id="selectForm" action="">
                <input type="hidden" id='ids' name='ids' value='' />
                <input type="hidden" id='_method' name='_method' value='' />
                <div class="panel-body">
                    {{ csrf_field() }}
                    <table class="table table-hover">
                        <thead>
                            <th class="text-center"><input type="checkbox" name="chkAll" onclick="checkAll(this.form)"/></th>
                            <th class="text-center">그룹아이디</th>
                            <th class="text-center">그룹</th>
                            <th class="text-center">처리일시</th>
                        </thead>

                        <tbody>
                        @foreach ($groups as $group)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="chk[]" class="groupId" value='{{ $group->pivot->id }}' /></td>
                                <td class="text-center">{{ $group->group_id }}</td>
                                <td class="text-center">{{ $group->subject }}</td>
                                <td class="text-center">{{ $group->pivot->created_at }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="panel-heading">
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
        var selected_id_array = selectIdsByCheckBox(".groupId");

        if(selected_id_array.length == 0) {
            alert('게시판 그룹을 선택해 주세요.')
            return;
        }

        $('#ids').val(selected_id_array);
        $('#_method').val('DELETE');
        $('#selectForm').attr('action', '/admin/accessible_groups' + '/' + {{ $user->id}});
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

function checkAll(form) {
    var chk = document.getElementsByName("chk[]");

    for (i=0; i<chk.length; i++) {
        chk[i].checked = form.chkAll.checked;
    }
}
</script>
@endsection
