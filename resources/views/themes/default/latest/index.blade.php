<div class="container">
    <div class="row">
        @forelse($boardList as $writes)
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
                    <a href="{{ route('board.view', ['boardName'=>$writes->table_name, 'writeId'=>$write->id]) }}">
                        <li>
                            <span class="lt_subject">{!! clean($write->subject)!!}</span>
                            @php
                                $createdDate = new Carbon\Carbon($write->created_at);
                            @endphp
                            @if($write->file > 0)
                            <span class="lt_icon"><img src="/themes/default/images/icon_file.gif"></span> <!-- 파일 -->
                            @endif
                            @if($write->link1 || $write->link2)
                            <span class="lt_icon"><img src="/themes/default/images/icon_link.gif"></span> <!-- 링크 -->
                            @endif
                            @if(str_contains($write->option, 'secret'))
                            <span class="lt_icon"><img src="/themes/default/images/icon_secret.gif"></span> <!-- 비밀 -->
                            @endif
                            @if( date($createdDate->addHours(24)) > date("Y-m-d H:i:s", time()) && $writes->new )
                            <span class="lt_icon"><img src="/themes/default/images/icon_new.gif"></span> <!-- 새글 -->
                            @endif
                            @if($write->hit >= $writes->hot)
                            <span class="lt_icon"><img src="/themes/default/images/icon_hot.gif"></span> <!-- 인기 -->
                            @endif
                            <span class="lt_cmt">[{{ $write->comment }}]</span>
                            <span class="lt_date">
                                @if($createdDate->subHours(24)->toDateString() == Carbon\Carbon::now()->toDateString())
                                    @hourAndMin($createdDate)
                                @else
                                    @monthAndDay($createdDate)
                                @endif
                            </span>
                        </li>
                    </a>
                    @endforeach
                </ul>
            </div>
        </div>
        @empty
        @endforelse
    </div>
</div>
