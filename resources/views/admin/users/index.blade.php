@extends('admin.admin')

@section('title')
    회원 관리 | {{ Cache::get("config.homepage")->title }}
@endsection

@section('include_script')
    <script src="{{ asset('js/common.js') }}"></script>
@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>회원관리</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">회원관리</li>
            <li class="depth">회원목록</li>
        </ul>
    </div>
    <div class="pull-right">
        <ul class="mb_btn" style="margin-top:8px;">
            <li>
                <a class="btn btn-default" href="{{ route('admin.users.create')}}" role="button">회원추가</a>
            </li>
            <li>
                <input type="button" id="selected_update" class="btn btn-default" value="선택수정">
            </li>
            <li>
                <input type="button" id="selected_delete" class="btn btn-default" value="선택삭제">
            </li>
        </ul>
    </div>
</div>

<div class="body-contents">
    @if(Session::has('message'))
        <div class="alert alert-info">
            {{ Session::get('message') }}
        </div>
    @endif

    <div id="mb" class="">
        <ul class="mb_btn mb10 pull-left">
            <li>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sir pull-left">전체보기</a>
            </li>
            <li>
                <span class="total">총회원수 {{ $users->total() }}명 중, <a href="{{ route('admin.users.index') }}?kind=intercept">차단 {{ $interceptUsers }}명</a>, <a href="{{ route('admin.users.index') }}?kind=leave">탈퇴 {{ $leaveUsers }}명</a></span>
            </li>
        </ul>
        <div class="mb_sch mb10 pull-right">
            <form class="form-horizontal" role="form" method="GET" action="{{ route('admin.users.index') }}">
                <label for="" class="sr-only">검색대상</label>
                <select name="kind" id="">
                    <option value="email" @if($kind == 'email') selected @endif>회원이메일</option>
                    <option value="nick" @if($kind == 'nick') selected @endif>회원닉네임</option>
                    <option value="level" @if($kind == 'level') selected @endif>권한(회원 레벨)</option>
                    <option value="created_at" @if($kind == 'created_at') selected @endif>가입일</option>
                    <option value="today_login" @if($kind == 'today_login') selected @endif>최근접속일</option>
                    <option value="ip" @if($kind == 'ip') selected @endif>IP</option>
                    <option value="recommend" @if($kind == 'recommend') selected @endif>추천인</option>
                </select>
                <label for="" class="sr-only">검색어</label>
                <input type="text" name="keyword" @if($keyword != '') value="{{ $keyword }}" @endif class="search" required>
                <button type="submit" id="" class="search-icon">
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
                        <a class="adm_sort" href="{{ route('admin.users.index') }}?order=email&amp;direction={{$order=='email' ? $direction : 'asc'}}">회원이메일</a>
                    </th>
                    <th>
                        <a class="adm_sort" href="{{ route('admin.users.index') }}?order=nick&amp;direction={{$order=='nick' ? $direction : 'asc'}}">닉네임</a>
                        <ul class="dropdown-menu" role="menu">
                        </ul>
                    </th>
                    <th>
                        상태/<a class="adm_sort" href="{{ route('admin.users.index') }}?order=level&amp;direction={{$order=='level' ? $direction : 'desc'}}">권한</a>
                    </th>
                    <th>
                        <a class="adm_sort" href="{{ route('admin.users.index') }}?order=point&amp;direction={{$order=='point' ? $direction : 'desc'}}">포인트</a>
                    </th>
                    <th>
                        <a class="adm_sort" href="{{ route('admin.users.index') }}?order=created_at&amp;direction={{$order=='created_at' ? $direction : 'desc'}}">가입일</a>
                    </th>
                    <th>
                        <a class="adm_sort" href="{{ route('admin.users.index') }}?order=today_login&amp;direction={{$order=='today_login' ? $direction : 'desc'}}">최근접속</a>
                    </th>
                    <th>접근그룹</th>
                    <th>관리</th>
                </thead>
                <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td><input type="checkbox" name="chkId[]" class="userId" value='{{ $user->id }}' /></td>
                        <td class="text-left">
                            <div class="mb_tooltip">
                                {{ $user->email }}
                                <span class="tooltiptext">{{ $user->ip }}</span>
                            </div>
                        </td>
                        <td class="td_nick">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">{{ $user->nick }}</a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ route('memo.create') }}?to={{ $user->id }}" class="winMemo" target="_blank" onclick="winMemo(this.href); return false;">쪽지보내기</a></li>
                                <li><a href="#">메일보내기</a></li>
                                <li><a href="{{ route('user.profile', $user->id) }}" class="winProfile" target="_blank" onclick="winProfile(this.href); return false;">자기소개</a></li>
        		                <li><a href="{{ route('admin.users.edit', $user->id) }}" target="_blank">회원정보변경</a></li>
        		                <li><a href="{{ route('admin.points.index') }}?kind=email&amp;keyword={{ $user->email }}" target="_blank">포인트내역</a></li>
                                <li><a href="{{ route('new.index') }}?nick={{ $user->nick }}">전체게시물</a></li>
                            </ul>
                        </td>
                        <td>
                        @if(!is_null($user->leave_date))
                            <span class="mb_msg withdraw">탈퇴</span>
                        @elseif (!is_null($user->intercept_date))
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
                        <td>{{ $user->point }}</td>
                        <td>
                            <div class="mb_tooltip">
                                @date($user->created_at)
                                <span class="tooltiptext">{{ $user->created_at }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="mb_tooltip">
                                @date($user->today_login)
                                <span class="tooltiptext">{{ $user->today_login }}</span>
                            </div>
                        </td>
                        <td>
                            @if($user->count_groups > 0)
                                <a href="{{ route('admin.accessGroups.show', $user->id) }}">
                                    {{ $user->count_groups }}
                                </a>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.users.edit', $user->id) }}">수정</a>
                            <a href="{{ route('admin.accessGroups.show', $user->id) }}">그룹</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            </form>

            {{-- 페이지 처리 --}}
            {{ str_contains(url()->current(), 'search')
                ? $users->appends([
                    'admin_page' => 'user',
                    'kind' => $kind,
                    'keyword' => $keyword,
                ])->links()
                : $users->links()
            }}
    </div>
</div>

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
        <?php $ids=''; ?>
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

function toUpdateBySelectOption(id, selected_id_array) {
    var send_array = Array();
    for(i=0; i<selected_id_array.length; i++) {
        send_array[i] = $('select[id=' + id + '_' + selected_id_array[i] + ']').val();
    }

    return send_array;
}
</script>
@endsection
