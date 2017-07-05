@extends('admin.admin')

@section('title')
    글, 댓글 현황 | {{ cache("config.homepage")->title }}
@endsection

@section('content')
<div id="mb" class="">
    <div class="mb_sch mb10">
        <form method="get" action="{{ route('admin.status') }}">
            {{ csrf_field() }}
            <label for="boardId" class="sr-only">게시판종류</label>
            <select name="boardId" id="boardId">
                <option value="" @if(!$selectBoard) selected @endif>전체게시판</option>
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
            <button type="submit" id="" class="search-icon">
                <i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">검색</span>
            </button>
        </form>
    </div>
</div>

@if( $chart )
    <div id="chart"></div>
    @if($selectType == 'line' || !$selectType)
        @linechart('Chart', 'chart')
        {{-- {{ Lava::render('LineChart','Chart', 'chart') }} --}}
    @else
        @columnchart('Chart', 'chart')
    @endif
@else
    <div>
        {{ $message }}
    </div>
@endif

@endsection

<script>
    var menuVal = 300500;
</script>
