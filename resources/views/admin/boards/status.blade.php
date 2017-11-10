@extends('admin.layouts.basic')

@section('title')글, 댓글 현황 | {{ cache("config.homepage")->title }}@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>글,댓글현황</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">게시판관리</li>
            <li class="depth">글,댓글현황</li>
        </ul>
    </div>
</div>
<div id="body_tab_type2">
    <span class="txt">지정한 기간동안의 글,댓글현황을 그래프로 보여줍니다.</span>
</div>

<div class="body-contents">
    <div id="adm_sch">
        <form method="get" action="{{ route('admin.status') }}">
            {{ csrf_field() }}
            <label for="boardId" class="sr-only">게시판종류</label>
            <select name="boardId" id="boardId">
                <option value="" @unless($selectBoard) selected @endunless>전체게시판</option>
                @foreach($boards as $board)
                    <option value="{{ $board->id }}" @if($selectBoard == $board->id) selected @endif>{{ $board->subject }}</option>
                @endforeach
            </select>
            <label for="period" class="sr-only">시점</label>
            <select name="period" id="period">
                @foreach($periods as $key => $value)
                    <option value="{{ $key }}" @if($selectPeriod == $key) selected @endif>{{ $key }}</option>
                @endforeach
            </select>
            <label for="type" class="sr-only">그래프종류</label>
            <select name="type" id="type">
                <option value="line" @if($selectType == 'line') selected @endif>선 그래프</option>
                <option value="bar" @if($selectType == 'bar') selected @endif>막대 그래프</option>
            </select>
            <button type="submit" id="search" class="btn search-icon">
                <i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">검색</span>
            </button>
        </form>
    </div>

    <div class="box" style="clear:both;">
@if( $chart )
    <div id="chart"></div>
    @if($selectType == 'line' || !$selectType)
        {{-- @linechart('Chart', 'chart') --}}
        {!! Lava::render('LineChart','Chart', 'chart') !!}
    @else
        {{-- @columnchart('Chart', 'chart') --}}
        {!! Lava::render('ColumnChart','Chart', 'chart') !!}
    @endif
@else
    <div>
        <span class="empty_table">
            <i class="fa fa-exclamation-triangle"></i> {{ $message }}
        </span>
    </div>
@endif
    </div>
</div>
@endsection

<script>
    var menuVal = 300400;
</script>
