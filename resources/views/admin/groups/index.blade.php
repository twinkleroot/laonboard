@extends('admin.admin')

@section('title')
    게시판 그룹 설정 | {{ Cache::get("config.homepage")->title }}
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
            <div class="panel-heading">게시판 그룹 설정</div>
            <div class="panel-heading">
                <a href="{{ route('admin.groups.index') }}" >전체목록</a> | 전체그룹 {{ $groups->total() }}개
            </div>
            <form method="GET" id="searchForm" action="{{ route('admin.groups.index') }}">
                <div class="panel-heading">
                    <select name="kind" id="kind">
                        <option value="subject" @if($kind == 'subject') selected @endif>제목</option>
                        <option value="group_id" @if($kind == 'group_id') selected @endif>그룹 ID</option>
                        <option value="admin" @if($kind == 'admin') selected @endif>그룹관리자</option>
                    </select>
                    <input type="text" id="keyword" name="keyword" @if($keyword != '') value="{{ $keyword }}" @endif />
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
                {{ csrf_field() }}
                <div class="panel-body">
                    <table class="table table-hover">
                    <thead>
                        <th class="text-center"><input type="checkbox" name="chkAll" onclick="checkAll(this.form)"/></th>
                        <th class="text-center">
                            <a class="mb_tooltip" href="{{ route('admin.groups.index'). $queryString }}&amp;order=group_id&amp;direction={{$order=='group_id' ? $direction : 'asc'}}">그룹 ID</a>
                        </th>
                        <th class="text-center">
                            <a class="mb_tooltip" href="{{ route('admin.groups.index'). $queryString }}&amp;order=subject&amp;direction={{$order=='subject' ? $direction : 'asc'}}">제목</a>
                        </th>
                        <th class="text-center">
                            <a class="mb_tooltip" href="{{ route('admin.groups.index'). $queryString }}&amp;order=admin&amp;direction={{$order=='admin' ? $direction : 'asc'}}">그룹관리자</a>
                        </th>
                        <th class="text-center">게시판</th>
                        <th class="text-center">접근<br />사용</th>
                        <th class="text-center">접근<br />회원수</th>
                        <th class="text-center">
                            <a class="mb_tooltip" href="{{ route('admin.groups.index'). $queryString }}&amp;order=order&amp;direction={{$order=='order' ? $direction : 'asc'}}">출력순서</a>
                        </th>
                        <th class="text-center">접속기기</th>
                        <th class="text-center">관리</th>
                    </thead>

                    <tbody>
                    @if(count($groups) > 0)
                    @foreach ($groups as $group)
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="chkId[]" class="groupId" value='{{ $group->id }}' />
                            </td>
                            <td class="text-center">
                                <a href="{{ route('group', $group->id) }}">{{ $group->group_id }}</a>
                            </td>
                            <td class="text-center">
                                <input type="text" id='subject_{{ $group->id }}' value='{{ $group->subject }}' />
                            </td>
                            <td class="text-center">
                                <input type="text" id='admin_{{ $group->id }}' value='{{ $group->admin }}' />
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.boards.index'). "?kind=group_id&keyword=". $group->group_id  }}">{{ $group->count_board }}</a>
                            </td>
                            <td class="text-center">
                                <input type='checkbox' id='use_access_{{ $group->id }}' value='1'
                                    {{ ($group->use_access == '1' ? 'checked' : '') }}/>
                                </td>
                            <td class="text-center">
                                <a href="{{ route('admin.accessUsers.show', $group->id)}}">{{ $group->count_users }}</a>
                            </td>
                            <td class="text-center">
                                <input type="text" id='order_{{ $group->id }}' value='{{ $group->order }}' />
                            </td>
                            <td class="text-center">
                                <select id='device_{{ $group->id }}'>
                                    <option value='both' {{ $group->device == 'both' ? 'selected' : '' }}>both</option>
                                    <option value='pc' {{ $group->device == 'pc' ? 'selected' : '' }}>pc</option>
                                    <option value='mobile' {{ $group->device == 'mobile' ? 'selected' : '' }}>mobile</option>
                                </select>
                            </td>
                            <td class="text-center">
                                <a class="btn btn-primary" href="{{ route('admin.groups.edit', $group->id). '?'. Request::getQueryString() }}">수정</a>
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
                    <a class="btn btn-primary" href={{ route('admin.groups.create') }}>게시판 그룹 추가</a>
                </div>
            </form>

            {{-- 페이지 처리 --}}
            {{ $groups->appends(Request::except('page'))->links() }}

        </div>
    </div>
</div>
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
        <?php $ids=''; ?>
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
</script>
@endsection
