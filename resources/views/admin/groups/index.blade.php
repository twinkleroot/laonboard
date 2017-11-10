@extends('admin.layouts.basic')

@section('title')게시판 그룹 설정 | {{ Cache::get("config.homepage")->title }}@endsection

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>게시판 그룹 관리</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">게시판 관리</li>
            <li class="depth">게시판 그룹 관리</li>
        </ul>
    </div>
</div>
<div id="body_tab_type2">
    <span class="txt">전체그룹 {{ $groups->total() }}개</span>

    <div class="submit_btn">
        @unless(isDemo())
        <a class="btn btn-default" href="{{ route('admin.groups.create') }}" role="button">게시판그룹 추가</a>
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
            <li><a href="{{ route('admin.groups.index') }}" class="btn btn-sir" role="button">전체목록</a></li>
            <li><input type="button" id="selected_update" class="btn btn-sir" value="선택수정"/></li>
            <li><input type="button" id="selected_delete" class="btn btn-sir" value="선택삭제"/></li>
        </ul>
        <div id="adm_sch">
             <form role="form" method="GET" action="{{ route('admin.groups.index') }}">
                <label for="kind" class="sr-only">검색대상</label>
                <select name="kind" id="kind">
                    <option value="subject" @if($kind == 'subject') selected @endif>제목</option>
                    <option value="group_id" @if($kind == 'group_id') selected @endif>그룹 ID</option>
                    <option value="admin" @if($kind == 'admin') selected @endif>그룹관리자</option>
                </select>
                <label for="keyword" class="sr-only">검색어</label>
                <input type="text" name="keyword" class="search" value="{{ $keyword }}" />
                <button type="submit" id="search" class="btn search-icon">
                    <i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">검색</span>
                </button>
            </form>
        </div>

        <form class="form-horizontal" role="form" method="POST" id="selectForm" action="">
            <input type="hidden" id='ids' name='ids' value='' />
            <input type="hidden" id='subjects' name='subjects' value='' />
            <input type="hidden" id='admins' name='admins' value='' />
            <input type="hidden" id='use_accesss' name='use_accesss' value='' />
            <input type="hidden" id='orders' name='orders' value='' />
            <input type="hidden" id='devices' name='devices' value='' />
            <input type="hidden" id='_method' name='_method' value='' />
            {{ csrf_field() }}
                <table class="table table-striped box">
                    <thead>
                        <th><input type="checkbox" name="chkAll" onclick="checkAll(this.form)"/></th>
                        <th>
                            <a class="adm_sort" href="{{ route('admin.groups.index'). $queryString }}&amp;order=group_id&amp;direction={{$order=='group_id' ? $direction : 'asc'}}">그룹 ID</a>
                        </th>
                        <th>
                            <a class="adm_sort" href="{{ route('admin.groups.index'). $queryString }}&amp;order=subject&amp;direction={{$order=='subject' ? $direction : 'asc'}}">제목</a>
                        </th>
                        <th>
                            <a class="adm_sort" href="{{ route('admin.groups.index'). $queryString }}&amp;order=admin&amp;direction={{$order=='admin' ? $direction : 'asc'}}">그룹관리자</a>
                        </th>
                        <th>게시판</th>
                        <th>접근<br>사용</th>
                        <th>접근<br>회원수</th>
                        <th>
                            <a class="adm_sort" href="{{ route('admin.groups.index'). $queryString }}&amp;order=order&amp;direction={{$order=='order' ? $direction : 'asc'}}">출력순서</a>
                        </th>
                        {{-- <th>접속기기</th> --}}
                        <th>관리</th>
                    </thead>

                    <tbody>
                    @forelse ($groups as $group)
                        <tr data-group-id="{{ $group->group_id }}">
                            <td class="td_chk">
                                <input type="checkbox" name="chkId[]" class="groupId" value='{{ $group->id }}' />
                            </td>
                            <td class="td_group">
                                <a href="{{ route('group', $group->group_id) }}">{{ $group->group_id }}</a>
                            </td>
                            <td>
                                <input type="text" id="subject_{{ $group->id }}" class="form-control" value="{{ $group->subject }}" />
                            </td>
                            <td class="td_email">
                                <input type="text" id="admin_{{ $group->id }}" class="form-control" value='{{ $group->admin }}' />
                            </td>
                            <td class="td_numsmall">
                                <a href="{{ route('admin.boards.index'). "?kind=group_id&keyword=". $group->group_id  }}">{{ $group->count_board }}</a>
                            </td>
                            <td class="td_chk">
                                <input type='checkbox' id='use_access_{{ $group->id }}' value='1'
                                    {{ ($group->use_access == '1' ? 'checked' : '') }}/>
                                </td>
                            <td class="td_numsmall">
                                <a href="{{ route('admin.accessUsers.show', $group->id)}}">{{ $group->count_users }}</a>
                            </td>
                            <td class="td_mngsmall">
                                <input type="text" id='order_{{ $group->id }}' class="form-control" value='{{ $group->order }}' />
                            </td>
                            {{-- <td class="td_mngsmall">
                                <select id='device_{{ $group->id }}' class="form-control">
                                    <option value='both' {{ $group->device == 'both' ? 'selected' : '' }}>both</option>
                                    <option value='pc' {{ $group->device == 'pc' ? 'selected' : '' }}>pc</option>
                                    <option value='mobile' {{ $group->device == 'mobile' ? 'selected' : '' }}>mobile</option>
                                </select>
                            </td> --}}
                            <td class="td_mngsmall">
                                <a href="{{ route('admin.groups.edit', $group->group_id). '?'. Request::getQueryString() }}">수정</a>
                            </td>
                        </tr>
                    @empty
                    <tr>
                        <td colspan="10">
                            <span class="empty_table">
                                <i class="fa fa-exclamation-triangle"></i> 자료가 없습니다.
                            </span>
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
        </form>

        {{-- 페이지 처리 --}}
        {{ $groups->appends(Request::except('page'))->links() }}
    </div>
</div>
@php $ids = ''; @endphp
<script>
var menuVal = 300200;
$(function(){
    // 선택 삭제 버튼 클릭
    $('#selected_delete').click(function(){
        var selected_id_array = selectIdsByCheckBox(".groupId");

        if(selected_id_array.length == 0) {
            alert('게시판 그룹을 선택해 주세요.')
            return;
        }

        if( !confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?")) {
            return;
        }

        $('#ids').val(selected_id_array);
        $('#_method').val('DELETE');
        $('#selectForm').attr('action', '{!! route('admin.groups.destroy', $ids) !!}' + '/' + selected_id_array);
        $('#selectForm').submit();
    });

    // 선택 수정 버튼 클릭
    $('#selected_update').click(function(){

        var selected_id_array = selectIdsByCheckBox(".groupId");

        if(selected_id_array.length == 0) {
            alert('게시판 그룹을 선택해 주세요.');
            return;
        }

        if(!formValidate(selected_id_array)) {
            return false;
        }

        // 목록에서 제목, 그룹관리자, 접근사용, 출력순서, 접속기기 변경 가능
        var subject_array = toUpdateByText("subject", selected_id_array);
        var admin_array = toUpdateByText("admin", selected_id_array);
        var order_array = toUpdateByText("order", selected_id_array);
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

function formValidate(selected_id_array) {
    $("#adm_save").remove();
    $(".body-contents td").removeClass('has-error');

    var message = '';
    selected_id_array.forEach (function (v, i) {
        var groupId = $('input[id=subject_' + v + ']').closest('tr').attr('data-group-id');
        if($('input[id=subject_' + v + ']').val() == '') {
            $('input[id=subject_' + v + ']').closest('td').addClass('has-error');
            message += "<span class=\"adm_save_txt\">" + '그룹 ID가 ' + groupId + '인 그룹의 제목을 입력해 주세요.' + "</span><br>";
        }
        var regex = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
        var admin = $('input[id=admin_' + v + ']').val();
        if(admin != '' && !regex.test(admin)) {
            $('input[id=admin_' + v + ']').closest('td').addClass('has-error');
            message += "<span class=\"adm_save_txt\">" + '그룹 ID가 ' + groupId + '인 그룹의 그룹관리자에 올바른 이메일 형식으로 입력해 주세요.' + "</span><br>";
        }
        if(isNaN($('input[id=order_' + v + ']').val())) {
            $('input[id=order_' + v + ']').closest('td').addClass('has-error');
            message += "<span class=\"adm_save_txt\">" + '그룹 ID가 ' + groupId + '인 그룹의 출력순서에 숫자를 입력해 주세요.' + "</span><br>";
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
