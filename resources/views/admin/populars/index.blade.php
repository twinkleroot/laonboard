@extends('admin.layouts.basic')

@section('title')인기검색어관리 | {{ Cache::get("config.homepage")->title }}@endsection

@section('include_css')
<link rel="stylesheet" type="text/css" href="{{ ver_asset('bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
<script src="{{ ver_asset('bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ ver_asset('bootstrap-datepicker/js/locales/bootstrap-datepicker.kr.min.js') }}" charset="UTF-8"></script>
@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>인기검색어관리</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">게시판관리</li>
            <li class="depth">인기검색어관리</li>
        </ul>
    </div>
</div>
<div id="body_tab_type2">
    <span class="txt">건수 {{ $populars->total() }}개</span>
</div>
<div class="body-contents">
    <div id="auth_list">
        <ul id="adm_btn">
            <li>
                <button type="button" class="btn btn-sir" onclick="location.href='{{ route('admin.populars.index') }}'">
                     전체목록
                </button>
            </li>
            <li>
                <button type="button" class="btn btn-sir pull-left" id="selected_delete">선택삭제</button>
            </li>
        </ul>

        <div id="adm_sch">
            <form class="form-horizontal" role="form" method="GET" action="{{ route('admin.populars.index') }}" onsubmit="return onSearchSubmit(this);">
                <label for="" class="sr-only">검색대상</label>
                <select name="kind" onchange="changeInput(this.value)">
                    <option value="word" @if($kind == 'word') selected @endif>검색어</option>
                    <option value="date" @if($kind == 'date') selected @endif>등록일</option>
                </select>

                <label for="keyword" class="sr-only">검색어</label>
                <input type="text" id="keyword" name="keyword" value="{{ $keyword }}" @if(!$kind || $kind == 'word') class="search" @else class="search datepicker" data-provide="datepicker" data-date-end-date="0d" @endif required>

                <button type="submit" class="btn search-icon">
                    <i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">검색</span>
                </button>
            </form>
        </div>

        <form class="form-horizontal" role="form" method="POST" id="selectForm" action="" onsubmit="return onSubmit(this);">
            {{ csrf_field() }}
            {{ method_field('delete') }}
            <input type="hidden" id="ids" value="" />
            <table class="table table-striped box">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" name="chkAll" onclick="checkAll(this.form)">
                        </th>
                        <th><a class="adm_sort" href="{{ route('admin.populars.index'). "?kind=$kind&keyword=$keyword&direction=$direction&order=word" }}">검색어</a></th>
                        <th>등록일</th>
                        <th>등록IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($populars as $popular)
                    <tr>
                        <td class="td_chk">
                            <input type="checkbox" name="chkId[]" class="popularId" value='{{ $popular->id }}' />
                        </td>
                        <td class="td_subject">
                            <a href="{{ route('admin.populars.index'). "?kind=word&keyword=". $popular->word }}">{{ $popular->word }}</a>
                        </td>
                        <td class="td_mngsmall">{{ $popular->date }}</td>
                        <td class="td_mngsmall">{{ $popular->ip }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11">
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

{{-- 페이지 처리 --}}
{{ $populars->appends(Request::except('page'))->links() }}

<script>
$(function(){
    $.fn.datepicker.dates['en'] = {
        today: "오늘",
        clear: "닫기",
        titleFormat: "yyyy년 MM"
    };
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        language: 'kr',
        clearBtn: true,
        todayBtn: true,
        todayHighlight: true,
        disableTouchKeyboard: true
    });
    // 선택 삭제 버튼 클릭
    $('#selected_delete').click(function(){
        var selected_id_array = selectIdsByCheckBox(".popularId");

        if(selected_id_array.length == 0) {
            alert('선택삭제 하실 항목을 하나 이상 선택하세요.');
            return;
        }

        $('#ids').val(selected_id_array);
        $('#selectForm').attr('action', '/admin/populars/destroy/' + selected_id_array);
        $('#selectForm').submit();
    });
});

// 검색 기준 변경에 따라서 input 박스의 설정을 바꾼다.
function changeInput(value) {
    if(value == 'word') {
        $(".datepicker").datepicker('destroy');
        $('#keyword').removeClass('datepicker');
        $('#keyword').attr('data-provide', '');
        $('#keyword').attr('data-date-end-date', '');
        $('#keyword').val('');
    } else {
        $('#keyword').addClass('datepicker');
        $('#keyword').attr('data-provide', 'datepicker');
        $('#keyword').attr('data-date-end-date', '0d');
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            language: 'kr',
            clearBtn: true,
            todayBtn: true,
            todayHighlight: true,
            disableTouchKeyboard: true
        });
        $('#keyword').val('');
    }
}

function onSearchSubmit(form) {
    form.keyword.value = document.getElementById(form.kind.value).value;

    alert(form.keyword.value);

    return true;
}

function onSubmit(form) {
    if(!confirm("선택한 항목을 정말 삭제하시겠습니까?")) {
        return false;
    }

    return true;
}

var menuVal = 300300;
</script>
@endsection
