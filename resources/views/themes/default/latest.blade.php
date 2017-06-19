<div class="container">
<div class="row">

@if(count($latestList))
@foreach($latestList as $latest)
<div class="col-md-4 col-sm-6 col-xs-12">
    <div class="lt">
        <div class="lt_head">
            <div class="lt_board pull-left">{{ $latest->board_subject }}</div>
            <a href="{{ route('board.index', $latest->board_id) }}">
                <span class="lt_more pull-right"></span>
            </a>
        </div>
        <ul class="lt_list">
            @foreach($latest as $write)
            <li>
                <span class="lt_subject">
                    <a href="{{ route('board.view', ['boardId'=>$latest->board_id, 'writeId'=>$write->id]) }}">{{ $write->subject }}</a>
                </span>
                <span class="lt_cmt">{{ $write->comment }}</span>
                <span class="lt_date">@date($write->created_at)</span>
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endforeach
@endif

</div>
</div>
