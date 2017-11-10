@extends('admin.layouts.basic')

@section('title')관리 권한 설정 | {{ Cache::get('config.homepage')->title }}@endsection

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>관리 권한 설정</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">환경 설정</li>
            <li class="depth">관리 권한 설정</li>
        </ul>
    </div>
</div>
<div id="body_tab_type2">
    <span class="txt">접근 가능 메뉴의 관리 권한을 설정합니다.</span>
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
    @if ($errors->any())
    <div id="adm_save">
        <span class="adm_save_txt">{{ $errors->first() }}</span>
        <button onclick="alertclose()" class="adm_alert_close">
            <i class="fa fa-times"></i>
        </button>
    </div>
    @endif

    <div id="auth_list">
        <ul id="adm_btn">
            <li>
                <button type="button" class="btn btn-sir" onclick="location.href='{{ route('admin.manageAuth.index') }}'">
                     전체보기
                </button>
            </li>
            <li>
                <button type="button" class="btn btn-sir" id="selected_delete">선택삭제</button>
            </li>
            <li>
                <span>
                    설정된 관리 권한 {{ $manageAuthList->total() }}건
                </span>
            </li>
        </ul>

        <div id="adm_sch">
            <form class="form-horizontal" role="form" method="GET" action="{{ route('admin.manageAuth.index') }}">
                <label for="keyword" class="sr-only">검색어</label>
                <input type="text" name="keyword" value="{{ $keyword }}" class="search" required>
                <button type="submit" class="btn search-icon">
                    <i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">검색</span>
                </button>
            </form>
        </div>
        <form class="form-horizontal" role="form" method="POST" id="selectForm" action="" onsubmit="return onSubmit(this);">
            {{ csrf_field() }}
            {{ method_field('delete') }}
            <table class="table table-striped box">
                <thead>
                    <tr>
                        <th class="td_chk">
                            <input type="checkbox" name="chkAll" onclick="checkAll(this.form)" />
                        </th>
                        <th>
                            <a class="mb_tooltip" href="{{ route('admin.manageAuth.index')."?keyword=$keyword&order=email&direction=" }}{{ $order == "email" ? $direction : "asc" }}">회원이메일</a>
                        </th>
                        <th>
                            <a class="mb_tooltip" href="{{ route('admin.manageAuth.index')."?keyword=$keyword&order=nick&direction=" }}{{ $order == "nick" ? $direction : "asc" }}">닉네임</a>
                        </th>
                        <th>메뉴</th>
                        <th>권한</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($manageAuthList as $auth)
                    <tr>
                        <td>
                            <input type="checkbox" name="chkId[]" class="authId" value='{{ $auth->id }}' />
                        </td>
                        <td class="td_email"><a href="{{ route('admin.manageAuth.index') }}?keyword={{ $auth->user_email }}">{{ $auth->user_email }}</a></td>
                        <td class="td_nick">
                            @component('admin.sideview', ['id' => $auth->user_id, 'nick' => $auth->user_nick, 'email' => $auth->user_email, 'created_at' => $auth->user_created_at])
                            @endcomponent
                        </td>
                        <td class="td_subject">{{ $auth->menu }}</td>
                        <td class="td_mngsmall">{{ $auth->auth }}</td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="5">
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
        {{ str_contains(url()->full(), 'keyword')
            ? $manageAuthList->appends([
                'keyword' => $keyword,
                'order' => $order,
                'direction' => $direction == 'desc' ? 'asc' : 'desc',
            ])->links()
            : $manageAuthList->links()
        }}
    </div>

    <div id="adm_alert">
        <span class="adm_alert_txt">다음 양식에서 회원에게 관리 권한을 부여하실 수 있습니다.<br>
        <strong>r</strong>은 읽기 권한, <strong>w</strong>는 쓰기 권한, <strong>d</strong>는 삭제 권한입니다.</span>
    </div>

    <div id="authlist_add" class="panel panel-default">
        <div class="panel-heading bg-sir">
            관리 권한 추가
        </div>

        <div class="panel-body row">
            <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.manageAuth.store') }}">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">회원 이메일</label>
                    <div class="col-sm-3">
                        <input type="email" class="form-control required" name="email" value="{{ $keyword }}" placeholder="Email">
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">접근가능메뉴</label>
                    <div class="col-sm-3">
                        <select class="form-control required" name="menu" placeholder="접근가능메뉴">
                            @foreach($menus as $key=>$value)
                                @if( !(substr($key, -3) == '000') )
                                    <option value="{{ $key }}">{{ $key. ' '. $value[0] }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">권한지정</label>
                    <div class="col-sm-3">
                        <input type="checkbox" name="r" value="r" id="r" checked><label for="r" name="authority">r (읽기)</label>
                        <input type="checkbox" name="w" value="w" id="w"><label for="w" name="authority">w (쓰기)</label>
                        <input type="checkbox" name="d" value="d" id="d"><label for="d" name="authority">d (삭제)</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" class="btn btn-sir">추가</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
var menuVal = 100200;
$(function(){
    // 선택 삭제 버튼 클릭
    $('#selected_delete').click(function(){
        var selected_id_array = selectIdsByCheckBox(".authId");

        if(selected_id_array.length == 0) {
            alert('선택삭제 하실 항목을 하나 이상 선택하세요.');
            return;
        }

        $('#ids').val(selected_id_array);
        $('#selectForm').attr('action', '/admin/manage/auths/' + selected_id_array);
        $('#selectForm').submit();
    });
});

function onSubmit(form) {
    if(!confirm("선택한 항목을 정말 삭제하시겠습니까?")) {
        return false;
    }

    return true;
}
</script>
@endsection
