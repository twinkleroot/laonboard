@extends('admin.layouts.basic')

@section('title')접근 가능 그룹 | {{ $config->title }}@endsection

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>접근 가능 그룹</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">회원 관리</li>
            <li class="depth">접근 가능 그룹</li>
        </ul>
    </div>
</div>
<div id="body_tab_type2">
    <span class="txt">이메일 <b>{{ $user->email }}</b>, 닉네임 <b>{{ $user->nick }}</b></span>

    <div class="submit_btn">

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
    <div id="adm_btn">
        <input type="button" id="selected_delete" class="btn btn-sir" value="선택삭제"/>
    </div>
    <div id="adm_sch">

        <form role="form" method="POST" action="{{ route('admin.accessGroups.store') }}">
            <input type="hidden" name="user_id" value="{{ $user->id }}" />
            {{ csrf_field() }}
            <label for="group_id" class="sr-only">그룹지정</label>
            <select name="group_id">
                <option>접근가능 그룹을 선택하세요.</option>
                @unless(isDemo())
                @foreach($accessible_groups as $accessible_group)
                    <option value="{{ $accessible_group->id }}">{{ $accessible_group->subject }}</option>
                @endforeach
                @endunless
            </select>
            <button type="submit" id="search" class="btn btn-sir" style="margin-top: -8px;">
                추가
            </button>
        </form>
    </div>
    <div id="board">
        <form class="form-horizontal" role="form" method="POST" id="selectForm" action="">
            <input type="hidden" id='ids' name='ids' value='' />
            <input type="hidden" id='_method' name='_method' value='' />
                {{ csrf_field() }}
                <table class="table table-striped box">
                    <thead>
                        <tr>
                            <th><input type="checkbox" name="chkAll" onclick="checkAll(this.form)"/></th>
                            <th>그룹아이디</th>
                            <th>그룹</th>
                            <th>처리일시</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse ($groups as $group)
                        <tr>
                            <td class="td_chk">
                                <input type="checkbox" name="chkId[]" class="groupId" value='{{ $group->pivot->id }}' /></td>
                            <td class="td_id">{{ $group->group_id }}</td>
                            <td class="td_subject">{{ $group->subject }}</td>
                            <td class="td_date">{{ $group->pivot->created_at }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <span class="empty_table">
                                    <i class="fa fa-exclamation-triangle"></i> 자료가 없습니다.
                                </span>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
        </form>
    </div>
</div>
<script>
var menuVal = 200100;
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
</script>
@endsection
