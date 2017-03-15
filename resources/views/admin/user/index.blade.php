@extends('layouts.app')

@section('title')
    LaBoard | 회원 관리
@endsection

@section('content')
@if(Session::has('message'))
  <div class="alert alert-info">
    {{Session::get('message') }}
  </div>
@endif
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
            <div class="panel-heading">회원 관리</div>
            <div class="panel-heading"><a class="btn btn-primary" href={{ route('users.create')}}>회원 추가</a></div>
            <form class="form-horizontal" role="form" method="POST" id="selectForm" action="">
                <input type="hidden" id='ids' name='ids' value='' />
                <input type="hidden" id='opens' name='opens' value='' />
                <input type="hidden" id='mailings' name='mailings' value='' />
                <input type="hidden" id='smss' name='smss' value='' />
                <input type="hidden" id='levels' name='levels' value='' />
                <input type="hidden" id='_method' name='_method' value='' />
                <div class="panel-body">
                    {{ csrf_field() }}
                    <table class="table table-hover">
                        <thead>
                            <th class="text-center"><input type="checkbox" name="chkAll" onclick="checkAll(this.form)"/></th>
                            <th class="text-center">이메일</th>
                            <th class="text-center">이름</th>
                            <th class="text-center">닉네임</th>
                            {{-- <th>메일인증</th> --}}
                            <th class="text-center">정보<br />공개</th>
                            <th class="text-center">메일<br />수신</th>
                            <th class="text-center">SMS<br />수신</th>
                            {{-- <th>성인인증</th> --}}
                            {{-- <th>접근차단</th> --}}
                            <th class="text-center">휴대폰</th>
                            <th class="text-center">전화<br />번호</th>
                            <th class="text-center">상태<br />/권한</th>
                            <th class="text-center">포인트</th>
                            <th class="text-center">최종<br />접속</th>
                            <th class="text-center">가입일</th>
                            {{-- <th>접근그룹</th> --}}
                            <th class="text-center">관리</th>
                        </thead>

                        <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="chk[]" class="userId" value='{{ $user->id }}' /></td>
                                <td class="text-center">{{ $user->email }}</td>
                                <td class="text-center">{{ $user->name }}</td>
                                <td class="text-center">{{ $user->nick }}</td>
                                {{-- <td class="text-center">{{ $user->email_certify }}</td> --}}
                                <td class="text-center">
                                    <input type='checkbox' id='open_{{ $user->id }}' value='1' {{ ($user->open == '1' ? 'checked' : '') }}/></td>
                                <td class="text-center">
                                    <input type='checkbox' id='mailing_{{ $user->id }}' value='1' {{ ($user->mailing == '1' ? 'checked' : '') }}/></td>
                                <td class="text-center">
                                    <input type='checkbox' id='sms_{{ $user->id }}' value='1' {{ ($user->sms == '1' ? 'checked' : '') }}/></td>
                                {{-- <td class="text-center">
                                    <input type='checkbox' name='adult' value='1' {{ ($user->adult == '1' ? 'checked' : '') }}/></td> --}}
                                {{-- <td class="text-center">
                                    <input type='checkbox' name='intercept_date' value='1' {{ ($user->intercept_date == '1' ? 'checked' : '') }}/></td> --}}
                                <td class="text-center">{{ $user->hp }}</td>
                                <td class="text-center">{{ $user->tel }}</td>
                                <td class="text-center">
                                    @if($user->leave_date != '')
                                        탈퇴함
                                    @elseif ($user->intercept_date != '')
                                        차단됨
                                    @else
                                        정상
                                    @endif
                                    <select name='level[]' class='level'>
                                        @for ($i=1; $i<=10; $i++)
                                            <option value='{{ $i }}' {{ $user->level == $i ? 'selected' : '' }}>
                                                {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                </td>
                                <td class="text-center">{{ $user->point }}</td>
                                <td class="text-center">{{ $user->today_login }}</td>
                                <td class="text-center">@date($user->created_at)</td>
                                {{-- <td class="text-center">{{ $user->nick }}</td> --}}
                                <td class="text-center">
                                    <a class="btn btn-primary" href="{{ route('users.edit', $user->id) }}">수정</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="panel-heading">
                    <input type="button" id="selected_update" class="btn btn-primary" value="선택 수정"/>
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
        var id_array = selectIdsByCheckBox(".userId");

        $('#ids').val(id_array);
        $('#_method').val('DELETE');
        <?php $ids=''; ?>
        $('#selectForm').attr('action', '{!! route('users.destroy', $ids) !!}' + '/' + id_array);
        $('#selectForm').submit();
    });

    // 선택 수정 버튼 클릭
    $('#selected_update').click(function(){
        var id_array = selectIdsByCheckBox(".userId");
        var open_array = selectOthersByCheckBox("open", id_array);
        var mailing_array = selectOthersByCheckBox("mailing", id_array);
        var sms_array = selectOthersByCheckBox("sms", id_array);
        // var level_array = selectsBySelectOption(".level", id_array);

        $('#ids').val(id_array);
        alert($('#ids').val());
        $('#opens').val(open_array);
        $('#mailings').val(mailing_array);
        $('#smss').val(sms_array);
        // $('#levels').val(level_array);
        $('#_method').val('PUT');
        $('#selectForm').attr('action', '{!! route('users.selectedUpdate') !!}');
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

function selectOthersByCheckBox(className, id_array) {
    var send_array = Array();
    var send_cnt = 0;

    for(i=0; i<id_array.length; i++) {
        var chkbox = $('input[id= ' + className + '_' + id_array[i] + ']');
        if(chkbox.is(':checked')) {
            send_array[send_cnt] = chkbox.val();
        } else {
            send_array[send_cnt] = 0;
        }
        send_cnt++;
    }

    return send_array;
}

function selectsBySelectOption(className, id_array) {
    var send_array = Array();
    var send_cnt = 0;
    var selectOption = $(className);

    for(i=0; i<selectOption.length; i++) {
        if(selectOption[i].selected == true) {
            send_array[send_cnt] = selectOption[i].value;
            send_cnt++;
        }
    }

    return send_array;
}

function checkAll(form) {
    var chk = document.getElementsByName("chk[]");

    for (i=0; i<chk.length; i++)
        chk[i].checked = form.chkAll.checked;
}
</script>
@endsection
