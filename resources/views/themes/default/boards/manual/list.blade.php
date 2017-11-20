@section('fisrt_include_css')
<link rel="alternate" type="application/rss+xml" href="{{ url('rss') }}" title="RSS Feed {{ config('rss.title') }}">
@stop

<div id="sub_menu">
    <a href="{{ route('board.index', $board->table_name) }}">
        <h1>{{ $board->subject }}</h1>
    </a>
    <ul class="post_list">
        @forelse($writes as $write)
            <li class="post @if(isset($request->writeId) && $request->writeId == $write->id)reading @endif">
                <a href="/bbs/{{ $board->table_name }}/views/{{ $write->parent. (Request::getQueryString() ? '?'.Request::getQueryString() : '')}}" @if($write->reply != '')class="post_rpy" style="padding-left: calc(15px * {{ strlen($write->reply) }}" @endif>
                    {!! clean($write->subject) !!}
                </a>
            </li>
        @empty
            <li class="post"><a>게시물이 없습니다.</a></li>
        @endforelse
    </ul>
</div>
<div id="sub_search">
    <form method="get" action="{{ route('board.index', $board->table_name) }}" onsubmit="return searchFormSubmit(this);">
        <input type="hidden" name="kind" value="subject">
        <label for="keyword" class="sr-only">검색어</label>
        <input type="text" name="keyword" id="keyword" value="{{ $kind != 'user_id' ? $keyword : '' }}" class="search" required>
        <button type="submit" class="search-icon">
            <i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">검색</span>
        </button>
    </form>
</div>
<script>
function searchFormSubmit(f) {
    if(f.keyword.value.trim() == '') {
        alert('검색어 : 필수 입력입니다.');
        return false;
    }
    return true;
}
</script>
