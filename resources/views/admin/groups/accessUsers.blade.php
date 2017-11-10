@extends('admin.layouts.basic')

@section('title'){{ $group->subject }}그룹 접근 가능 회원 | {{ Cache::get("config.homepage")->title }}@endsection

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>'{{ $group->subject }}' 그룹 접근 가능 회원 (Group Id : {{ $group->group_id }})</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">게시판 그룹 관리</li>
            <li class="depth">게시판 그룹 접근 가능 회원</li>
        </ul>
    </div>
</div>
<div id="body_tab_type2">
    <span class="txt">'{{ $group->subject }}({{ $group->group_id }})'의 접근 가능 회원을 지정합니다.</span>
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
        <form role="form" method="GET" action="{{ route('admin.accessUsers.show', $group->id) }}">
            <label for="kind" class="sr-only">검색대상</label>
            <select name="kind" id="kind">
                <option value="nick">회원 닉네임</option>
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
        <input type="hidden" id='_method' name='_method' value='' />
        <div class="">
            {{ csrf_field() }}
            <table class="table table-striped box">
                <thead>
                    <th><input type="checkbox" name="chkAll" onclick="checkAll(this.form)"/></th>
                    <th>그룹</th>
                    <th>
                        <a class="adm_sort" href="{{ route('admin.accessUsers.show', $group->id). $queryString }}&amp;order=email&amp;direction={{$order=='email' ? $direction : 'asc'}}">회원이메일</a>
                    </th>
                    <th>
                        <a class="adm_sort" href="{{ route('admin.accessUsers.show', $group->id). $queryString }}&amp;order=nick&amp;direction={{$order=='nick' ? $direction : 'asc'}}">닉네임</a>
                    </th>
                    <th>
                        <a class="adm_sort" href="{{ route('admin.accessUsers.show', $group->id). $queryString }}&amp;order=today_login&amp;direction={{$order=='today_login' ? $direction : 'asc'}}">최근접속</a>
                    </th>
                    <th>
                        <a class="adm_sort" href="{{ route('admin.accessUsers.show', $group->id). $queryString }}&amp;order=created_at&amp;direction={{$order=='created_at' ? $direction : 'asc'}}">처리일시</a>
                    </th>
                </thead>

                <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td class="td_chk">
                            <input type="checkbox" name="chkId[]" class="userId" value='{{ $user->pivot->id }}' /></td>
                        <td class="td_numsmall">
                            <a href="{{ route('admin.accessGroups.show', $user->id) }}">{{ $user->count_groups }}</a>
                        </td>
                        <td class="td_subject">{{ $user->email }}</td>
                        <td class="td_nick">
                            @component('admin.sideview', ['id' => $user->id, 'nick' => $user->nick, 'email' => $user->email, 'created_at' => $user->created_at])
                            @endcomponent
                        </td>
                        <td class="td_date">@date($user->today_login)</td>
                        <td class="td_date">{{ $user->pivot->created_at }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <span class="empty_table">
                                <i class="fa fa-exclamation-triangle"></i> 자료가 없습니다.
                            </span>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="panel-heading">

        </div>
    </form>
    {{-- 페이지 처리 --}}
    {{ $users->appends(Request::except('page'))->links() }}
</div>
<script>
var menuVal = 300200;
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
