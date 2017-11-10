@extends('admin.layouts.basic')

@section('title')회원 관리 | {{ Cache::get("config.homepage")->title }}@endsection

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>회원 관리</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">회원 관리</li>
            <li class="depth">회원 관리</li>
        </ul>
    </div>
    <div class="pull-right">
        <ul class="mb_btn" style="margin-top:8px;">
            <li>

            </li>
        </ul>
    </div>
</div>
<div id="body_tab_type2">
    <span class="txt">총회원수 {{ $users->total() }}명 중, <a href="{{ route('admin.users.index') }}?kind=intercept">차단 {{ $interceptUsers }}명</a>, <a href="{{ route('admin.users.index') }}?kind=leave">탈퇴 {{ $leaveUsers }}명</a></span>

    <div class="submit_btn">
        @unless(isDemo())
        <a class="btn btn-default" href="{{ route('admin.users.create')}}" role="button">회원 추가</a>
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
    <div id="mb" class="">
        <ul id="adm_btn">
            <li><a href="{{ route('admin.users.index') }}" class="btn btn-sir pull-left">전체목록</a></li>
            <li><input type="button" id="selected_update" class="btn btn-sir" value="선택수정"></li>
            <li><input type="button" id="selected_delete" class="btn btn-sir" value="선택삭제"></li>
        </ul>

        <div id="adm_sch">
            <form role="form" method="GET" action="{{ route('admin.users.index') }}">
                <label for="kind" class="sr-only">검색대상</label>
                <select name="kind">
                    <option value="email" @if($kind == 'email') selected @endif>회원이메일</option>
                    <option value="nick" @if($kind == 'nick') selected @endif>회원닉네임</option>
                    <option value="level" @if($kind == 'level') selected @endif>권한(회원 레벨)</option>
                    <option value="created_at" @if($kind == 'created_at') selected @endif>가입일</option>
                    <option value="today_login" @if($kind == 'today_login') selected @endif>최근접속일</option>
                    <option value="ip" @if($kind == 'ip') selected @endif>IP</option>
                    <option value="recommend" @if($kind == 'recommend') selected @endif>추천인</option>
                </select>

                <label for="keyword" class="sr-only">검색어</label>
                <input type="text" name="keyword" @if($keyword != '') value="{{ $keyword }}" @endif class="search" required>
                <button type="submit" id="" class="btn search-icon">
                    <i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">검색</span>
                </button>
            </form>
        </div>

        <form class="form-horizontal" role="form" method="POST" id="selectForm" action="">
            <input type="hidden" id='ids' name='ids' value='' />
            <input type="hidden" id='opens' name='opens' value='' />
            <input type="hidden" id='mailings' name='mailings' value='' />
            <input type="hidden" id='smss' name='smss' value='' />
            <input type="hidden" id='intercepts' name='intercepts' value='' />
            <input type="hidden" id='levels' name='levels' value='' />
            <input type="hidden" id='_method' name='_method' value='' />
            {{ csrf_field() }}
            <table class="table table-striped box">
                <thead>
                    <th class="td_chk"><input type="checkbox" name="chkAll" onclick="checkAll(this.form)"/></th>
                    <th>
                        <a class="adm_sort" href="{{ route('admin.users.index'). $queryString }}&amp;order=email&amp;direction={{$order=='email' ? $direction : 'asc'}}">회원이메일</a>
                    </th>
                    <th>
                        <a class="adm_sort" href="{{ route('admin.users.index'). $queryString }}&amp;order=nick&amp;direction={{$order=='nick' ? $direction : 'asc'}}">닉네임</a>
                        <ul class="dropdown-menu" role="menu">
                        </ul>
                    </th>
                    <th>
                        상태/<a class="adm_sort" href="{{ route('admin.users.index'). $queryString }}&amp;order=level&amp;direction={{$order=='level' ? $direction : 'desc'}}">권한</a>
                    </th>
                    <th>
                        <a class="adm_sort" href="{{ route('admin.users.index'). $queryString }}&amp;order=point&amp;direction={{$order=='point' ? $direction : 'desc'}}">포인트</a>
                    </th>
                    <th>
                        <a class="adm_sort" href="{{ route('admin.users.index'). $queryString }}&amp;order=created_at&amp;direction={{$order=='created_at' ? $direction : 'desc'}}">가입일</a>
                    </th>
                    <th>
                        <a class="adm_sort" href="{{ route('admin.users.index'). $queryString }}&amp;order=today_login&amp;direction={{$order=='today_login' ? $direction : 'desc'}}">최근접속</a>
                    </th>
                    <th>접근그룹</th>
                    <th>관리</th>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="td_chk"><input type="checkbox" name="chkId[]" class="userId" value='{{ $user->id }}' /></td>
                        <td class="td_subject">
                            <div class="mb_tooltip">
                                {{ $user->email }}
                                <span class="tooltiptext">{{ $user->ip }}</span>
                            </div>
                        </td>
                        <td class="td_nick">
                            @component('admin.sideview', ['id' => $user->id, 'nick' => $user->nick, 'email' => $user->email, 'created_at' => $user->created_at])
                            @endcomponent
                        </td>
                        <td class="td_date">
                        @if($user->leave_date)
                            <span class="mb_msg withdraw">탈퇴</span>
                        @elseif ($user->intercept_date)
                            <span class="mb_msg intercept">차단</span>
                        @else
                            <span class="mb_msg">정상</span>
                        @endif
                            <select id='level_{{ $user->id }}'>
                                @for ($i=1; $i<=auth()->user()->level; $i++)
                                    <option value='{{ $i }}' {{ $user->level == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </td>
                        <td class="td_mngsmall">{{ number_format($user->point) }}</td>
                        <td class="td_date">
                            <div class="mb_tooltip">
                                @date($user->created_at)
                                <span class="tooltiptext">{{ $user->created_at }}</span>
                            </div>
                        </td>
                        <td class="td_date">
                            <div class="mb_tooltip">
                                @date($user->today_login)
                                <span class="tooltiptext">{{ $user->today_login }}</span>
                            </div>
                        </td>
                        <td class="td_mngsmall">
                            @if($user->count_groups > 0)
                                <a href="{{ route('admin.accessGroups.show', $user->id) }}">
                                    {{ $user->count_groups }}
                                </a>
                            @endif
                        </td>
                        <td class="td_mngsmall">
                            <a href="{{ route('admin.users.edit', $user->id). '?'. Request::getQueryString() }}">수정</a>
                            <a href="{{ route('admin.accessGroups.show', $user->id). '?'. Request::getQueryString() }}">그룹</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
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
        {{ $users->appends(Request::except('page'))->links() }}
    </div>
</div>

@php
    $ids = '';
@endphp
<script>
var menuVal = 200100;
$(function(){
    // 선택 삭제 버튼 클릭
    $('#selected_delete').click(function(){
        var selected_id_array = selectIdsByCheckBox(".userId");

        if(selected_id_array.length == 0) {
            alert('회원을 선택해 주세요.');
            return;
        }

        if( !confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?")) {
            return;
        }

        $('#ids').val(selected_id_array);
        $('#_method').val('DELETE');

        $('#selectForm').attr('action', '{!! route('admin.users.destroy', $ids) !!}' + '/' + selected_id_array);
        $('#selectForm').submit();
    });

    // 선택 수정 버튼 클릭
    $('#selected_update').click(function(){

        var selected_id_array = selectIdsByCheckBox(".userId");

        if(selected_id_array.length == 0) {
            alert('회원을 선택해 주세요.')
            return;
        }

        var open_array = toUpdateByCheckBox("open", selected_id_array);
        var mailing_array = toUpdateByCheckBox("mailing", selected_id_array);
        var sms_array = toUpdateByCheckBox("sms", selected_id_array);
        var intercept_array = toUpdateByCheckBox("intercept_date", selected_id_array);
        var level_array = toUpdateBySelectOption("level", selected_id_array);

        $('#ids').val(selected_id_array);
        $('#opens').val(open_array);
        $('#mailings').val(mailing_array);
        $('#smss').val(sms_array);
        $('#intercepts').val(intercept_array);
        $('#levels').val(level_array);
        $('#_method').val('PUT');
        $('#selectForm').attr('action', '{!! route('admin.users.selectedUpdate') !!}');
        $('#selectForm').submit();
    });

});
</script>
@endsection
