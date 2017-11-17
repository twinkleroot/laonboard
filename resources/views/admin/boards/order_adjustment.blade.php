@extends('admin.layouts.basic')

@section('title')게시글 순서 변경 | {{ cache("config.homepage")->title }}@stop

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
@stop

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>게시물 순서조정</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">게시판 관리</li>
            <li class="depth">게시판 수정</li>
            <li class="depth">게시물 순서조정</li>
        </ul>
    </div>
</div>
<div id="body_tab_type2">
    <span class="txt">게시글의 순서를 변경하면 해당 글에 달린 댓글, 답변글도 함께 변경됩니다.</span>
    <div class="submit_btn">
        @unless(isDemo())
        <button type="button" class="btn btn-sir" onclick="$('#listForm').submit();">선택한 게시물 순서변경</button>
        <a class="btn btn-default" href="{{ route('admin.boards.edit', $board->table_name). '?'. Request::getQueryString() }}">게시판 설정</a>
        <a class="btn btn-default" href="{{ route('board.index', $board->table_name) }}">게시판 바로가기</a>
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
    <div id="adjustorder">
        <form class="form-horizontal" role="form" method="post" id="inputForm" action="{{ route('admin.boards.adjustOrder') }}" onsubmit="return inputOnsubmit(this);">
            {{ csrf_field() }}
            {{ method_field('put') }}
            <input type="hidden" name="boardName" value="{{ $board->table_name }}">
            <label for="id_0" class="control-label">바꿀 게시물 1</label>
            <input type="text" class="form-control form_small" id="id_0" name="id[]" required>
            <label for="id_1" class="control-label">바꿀 게시물 2</label>
            <input type="text" class="form-control form_small" id="id_1" name="id[]" required>
            <button type="submit" class="btn btn-sir">순서 변경</button>
        </form>
    </div>
    <div id="mb">
        <form class="form-horizontal" role="form" method="post" id="listForm" action="{{ route('admin.boards.adjustOrder') }}" onsubmit="return listOnsubmit(this);">
            {{ csrf_field() }}
            {{ method_field('put') }}
            <input type="hidden" name="boardName" value="{{ $board->table_name }}">
            <table class="table table-striped box">
                <thead>
                    <th>선택</th>
                    <th>글번호</th>
                    <th>제목</th>
                    <th>작성자</th>
                    <th>작성일</th>
                </thead>
                <tbody>
                @forelse ($writes as $write)
                    <tr>
                        <td class="td_chk">
                            <input type="checkbox" name="id[]" class="writeId" value="{{ $write->id }}">
                        </td>
                        <td class="td_mngsmall">{{ $write->id }}</td>
                        <td class="td_subject">
                            <a href="/bbs/{{ $board->table_name }}/views/{{ $write->parent }}" class="bd_subject_title" target="_blank">
                                {{ subjectLength($write->subject, $board->subject_len) }}
                            </a>
                        </td>
                        <td class="td_nick">{{ $write->name }}</td>
                        <td class="td_date">
                            @if(date($write->created_at) == Carbon\Carbon::now()->toDateString())
                                @hourAndMin($write->created_at)
                            @else
                                @monthAndDay($write->created_at)
                            @endif
                        </td>
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
    </div>
</div>

{{ $writes->links() }}

<script>
var menuVal = 300100;

function listOnsubmit(form)
{
    var cnt = 0;
    $("#listForm input[name='id[]']").each(function () {
        if($(this).is(":checked")) {
            cnt++;
        }
    });
    if(cnt != 2) {
        alert('순서를 바꿀 게시물 2개만 선택해 주세요.');
        return false;
    }

    return true;
}

function inputOnsubmit(form)
{
    var result = true;
    $("#inputForm input[name='id[]']").each(function () {
        var regex = /[0-9]/g;
        if(!regex.test($(this).val())) {
            alert('게시물 번호는 숫자만 입력해 주세요.');
            $(this).val("");
            result = false;

            // break
            return false;
        }
    });

    return result;
}
</script>

@endsection
