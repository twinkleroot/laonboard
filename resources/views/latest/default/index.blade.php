<div class="container">
<div class="row">

@if(count($boardList))
@foreach($boardList as $writes)
<div class="col-md-4 col-sm-6 col-xs-12">
    <div class="lt">
        <div class="lt_head">
            <div class="lt_board pull-left">
                <a href="{{ route('board.index', $writes->board_id) }}">
                    {{ $writes->board_subject }}
                </a>
            </div>
            <a href="{{ route('board.index', $writes->board_id) }}">
                <span class="lt_more pull-right"></span>
            </a>
        </div>
        <ul class="lt_list">
            @foreach($writes as $write)
            <li>
                <span class="lt_subject">
                    <a href="{{ route('board.view', ['boardId'=>$writes->board_id, 'writeId'=>$write->id]) }}">{{ $write->subject }}</a>
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
