@extends('admin.layouts.basic')

@section('title')인기 검색어 순위 | {{ Cache::get("config.homepage")->title }}@endsection

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
        <h3>인기 검색어 순위</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">모듈 관리</li>
            <li class="depth">설치된 모듈</li>
            <li class="depth">인기 검색어 순위</li>
        </ul>
    </div>
</div>
<div id="body_tab_type2">
    <span class="txt">건수 {{ $ranks->total() }}개</span>
    <div class="submit_btn">
        <a class="btn btn-default" href="{{ route('admin.modules.index') }}">모듈목록</a>
    </div>
</div>
<div class="body-contents">
    <div id="auth_list">
        <ul id="adm_btn">
            <li>
                <button type="button" class="btn btn-danger" onclick="location.href='{{ route('admin.popular.index') }}'">인기 검색어 관리</button>
            </li>
            <li>
                <button type="button" class="btn btn-sir" onclick="location.href='{{ route('admin.popular.rank') }}?list=all';">
                     전체목록
                </button>
            </li>
            <li>
                <span>

                </span>
            </li>
        </ul>

        <div id="adm_sch" class="mb10 pull-right">
            <form class="form-horizontal" role="form" method="GET" action="{{ route('admin.popular.rank') }}">
                <label for="fromDate" class="control-label">기간별검색</label>
                <input type="text" id="fromDate" name="fromDate" value="{{ $fromDate }}" data-provide="datepicker" data-date-end-date="0d" class="period datepicker" required>
                <span style="font-size: 13px;">~</span>
                <input type="text" name="toDate" id="toDate" value="{{ $toDate }}" data-provide="datepicker" data-date-end-date="0d" class="period datepicker" required>
                <button type="submit" class="btn search-icon">
                    <i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">검색</span>
                </button>
            </form>
        </div>

        <table class="table table-striped box">
            <thead>
                <tr>
                    <th>순위</th>
                    <th>검색어</th>
                    <th>검색회수</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ranks as $rank)
                <tr>
                    <td class="td_numsmall">{{ $loop->iteration }}</td>
                    <td class="td_subject">{{ $rank->word }}</td>
                    <td class="td_mngsmall">{{ $rank->cnt }}</td>
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
    </div>

    {{-- 페이지 처리 --}}
    {{ $ranks->appends([
        'fromDate' => $fromDate,
        'toDate' => $toDate,
        ])->links() }}
</div>
<script>
    var menuVal = 400100;
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
    });
</script>
@endsection
