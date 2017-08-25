<div class="container">
<div class="row">

@if(count($boardList))
@foreach($boardList as $writes)
<div class="col-md-4 col-sm-6 col-xs-12">
    <div class="lt">
        <div class="lt_head">
            <div class="lt_board pull-left">
                <a href="{{ route('board.index', $writes->table_name) }}">
                    {{ $writes->board_subject }}
                </a>
            </div>
            <a href="{{ route('board.index', $writes->table_name) }}">
                <span class="lt_more pull-right"></span>
            </a>
        </div>
        <ul class="lt_list">
            @foreach($writes as $write)
            <li>
                <span class="lt_subject">
                    <a href="{{ route('board.view', ['boardName'=>$writes->table_name, 'writeId'=>$write->id]) }}">{{ $write->subject }}</a>
                </span>
                @php
                    $createdDate = new Carbon\Carbon($write->created_at);
                @endphp
                @if( date($createdDate->addHours(24)) > date("Y-m-d H:i:s", time()) && $writes->new)
                <span class="lt_icon"><img src="/themes/default/images/icon_new.gif"></span> <!-- 새글 -->
                @endif
                @if($write->file > 0)
                <span class="lt_icon"><img src="/themes/default/images/icon_file.gif"></span> <!-- 파일 -->
                @endif
                @if($write->link1 || $write->link2)
                <span class="lt_icon"><img src="/themes/default/images/icon_link.gif"></span> <!-- 링크 -->
                @endif
                @if($write->hit >= $writes->hot)
                <span class="lt_icon"><img src="/themes/default/images/icon_hot.gif"></span> <!-- 인기 -->
                @endif
                @if(str_contains($write->option, 'secret'))
                <span class="lt_icon"><img src="/themes/default/images/icon_secret.gif"></span> <!-- 비밀 -->
                @endif
                <span class="lt_cmt">[{{ $write->comment }}]</span>
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
