@extends('themes.default.basic')

@section('title')
    {{ $group->subject }}그룹 접근가능회원 | {{ $config->title }}
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
            <div class="panel-heading"><h2>'{{ $group->subject }}' 그룹 접근가능회원 (그룹아이디 : {{ $group->group_id }})</h2></div>
            <form class="form-horizontal" role="form" method="GET" action="{{ route('admin.search') }}">
                <input type="hidden" name="admin_page" value="accessibleUsers" />
                <input type="hidden" name="groupId" value="{{ $group->id }}" />
                 <p>
                    <select name="kind">
                        <option value="nick">회원 닉네임</option>
                    </select>
                    <input type="text" name="keyword" value="{{ $keyword }}" />
                    <input type="submit" class="btn btn-primary" value="검색" />
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
                            <th class="text-center">그룹</th>
                            <th class="text-center">회원이메일</th>
                            <th class="text-center">이름</th>
                            <th class="text-center">닉네임</th>
                            <th class="text-center">최종접속</th>
                            <th class="text-center">처리일시</th>
                        </thead>

                        <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="chkId[]" class="userId" value='{{ $user->pivot->id }}' /></td>
                                <td class="text-center">
                                    <a href="{{ route('admin.accessGroups.show', $user->id) }}">{{ $user->count_groups }}</a>
                                </td>
                                <td class="text-center">{{ $user->email }}</td>
                                <td class="text-center">{{ $user->name }}</td>
                                <td class="text-center">{{ $user->nick }}</td>
                                <td class="text-center">@date($user->today_login)</td>
                                <td class="text-center">{{ $user->pivot->created_at }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="panel-heading">
                    <input type="button" id="selected_delete" class="btn btn-primary" value="선택 삭제"/>
                </div>
            </form>

            {{-- 페이지 처리 --}}
            {{ str_contains(url()->current(), 'search')
                ? $users->appends([
                    'admin_page' => 'accessibleUsers',
                    'groupId' => $group->id,
                    'kind' => $kind,
                    'keyword' => $keyword,
                ])->links()
                : $users->links()
            }}

        </div>
    </div>
</div>
<script>
$(function(){
    // 선택 삭제 버튼 클릭
    $('#selected_delete').click(function(){
        var selected_id_array = selectIdsByCheckBox(".userId");

        if(selected_id_array.length == 0) {
            alert('회원을 선택해 주세요.');
            return;
        }

        $('#ids').val(selected_id_array);
        $('#_method').val('DELETE');
        $('#selectForm').attr('action', '/admin/accessible_users' + '/' + {{ $group->id}});
        $('#selectForm').submit();
    });

});
</script>
@endsection
