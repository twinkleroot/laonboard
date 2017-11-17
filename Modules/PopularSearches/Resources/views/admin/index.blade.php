@extends('admin.layouts.basic')

@section('title')인기 검색어 관리 | {{ Cache::get("config.homepage")->title }}@endsection

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
        <h3>인기 검색어 관리</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">모듈 관리</li>
            <li class="depth">설치된 모듈</li>
            <li class="depth">인기 검색어 관리</li>
        </ul>
    </div>
</div>
<form name="informForm" action="{{ route('admin.popular.update') }}" method="POST">
    {{ csrf_field() }}
    {{ method_field('put') }}
<div id="body_tab_type2">
    <span class="txt">인기 검색어 건수 {{ $populars->total() }}개</span>
    <div class="submit_btn">
        <button type="submit" class="btn btn-sir">설정변경</button>
        <a class="btn btn-default" href="{{ route('admin.modules.index') }}">모듈목록</a>
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
    @if ($errors->any())
    <div id="adm_save">
        <span class="adm_save_txt">{{ $errors->first() }}</span>
        <button onclick="alertclose()" class="adm_alert_close">
            <i class="fa fa-times"></i>
        </button>
    </div>
    @endif

    <div class="adm_box_hd">
        <span class="adm_box_title">인기 검색어 설정</span>
    </div>
    <section class="adm_box first">
        <table class="adm_box_table">
            <tr>
                <th>인기 검색어 삭제</th>
                <td class="table_body">
                    <input type="text" name="del" class="form-control form_num" value="{{ cache('config.popular')->del }}">
                    <span class="help-block">설정일이 지난 인기 검색어 자동 삭제</span>
                </td>
            </tr>
        </table>
    </section>
</div>
</form>

<div class="body-contents">
    <div id="auth_list">
        <ul id="adm_btn">
            <li>
                <button type="button" class="btn btn-danger" onclick="location.href='{{ route('admin.popular.rank') }}'">인기 검색어 순위</button>
            </li>
            <li>
                <button type="button" class="btn btn-sir" onclick="location.href='{{ route('admin.popular.index') }}'">전체목록</button>
            </li>
            <li>
                <button type="button" class="btn btn-sir pull-left" id="selected_delete">선택삭제</button>
            </li>
        </ul>

        <div id="adm_sch">
            <form class="form-horizontal" role="form" method="GET" action="{{ route('admin.popular.index') }}" onsubmit="return onSearchSubmit(this);">
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
                        <th><a class="adm_sort" href="{{ route('admin.popular.index'). "?kind=$kind&keyword=$keyword&direction=$direction&order=word" }}">검색어</a></th>
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
                            <a href="{{ route('admin.popular.index'). "?kind=word&keyword=". $popular->word }}">{{ $popular->word }}</a>
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
        $('#selectForm').attr('action', '/admin/popular');
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

var menuVal = 400100;
</script>
@endsection
