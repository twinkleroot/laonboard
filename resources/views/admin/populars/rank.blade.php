@extends('admin.admin')

@section('title')
    인기검색어순위 | {{ Cache::get("config.homepage")->title }}
@endsection

@section('include_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('include_script')
    <script src="{{ asset('js/common.js') }}"></script>
    <script src="{{ asset('bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('bootstrap-datepicker/js/locales/bootstrap-datepicker.kr.min.js') }}" charset="UTF-8"></script>
@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>인기검색어순위</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">게시판관리</li>
            <li class="depth">인기검색어순위</li>
        </ul>
    </div>
</div>
<div class="body-contents">
    <div id="auth_list">
        <ul id="adm_btn">
            <li>
                <button type="button" class="btn btn-sir" onclick="location.href='{{ route('admin.populars.rank') }}?list=all';">
                     전체목록
                </button>
            </li>
            <li>
                <span>
                    건수 {{ $ranks->total() }}개
                </span>
            </li>
        </ul>

        <div id="adm_sch" class="mb10 pull-right">
            <form class="form-horizontal" role="form" method="GET" action="{{ route('admin.populars.rank') }}">
                <label for="fromDate" class="control-label">기간별검색</label>
                <input type="text" id="fromDate" name="fromDate" value="{{ $fromDate }}" data-provide="datepicker" data-date-end-date="0d" class="period datepicker" required>
                <span>~</span>
                <input type="text" name="toDate" id="toDate" value="{{ $toDate }}" data-provide="datepicker" data-date-end-date="0d" class="period datepicker" required>
                <button type="submit" class="search2">
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
                @if(count($ranks) > 0)
                @foreach($ranks as $rank)
                <tr>
                    <td class="td_numsmall">{{ $loop->iteration }}</td>
                    <td class="td_subject">{{ $rank->word }}</td>
                    <td class="td_mngsmall">{{ $rank->cnt }}</td>
                </tr>
                @endforeach
                @else
                    <tr>
                        <td colspan="11">
                            <span class="empty_table">
                                <i class="fa fa-exclamation-triangle"></i> 자료가 없습니다.
                            </span>
                        </td>
                    </tr>
                @endif
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
    var menuVal = 300310;
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
