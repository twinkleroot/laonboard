@extends("themes.". cache('config.theme')->name. ".layouts.". ($board->layout ? : 'basic'))

@section('title'){{ $write->subject }} > {{ $board->subject }} | {{ cache('config.homepage')->title }}@stop

@section('include_script')
<script src="{{ ver_asset('js/viewimageresize.js') }}"></script>
<script src="{{ ver_asset('js/common.js') }}"></script>
@stop

@section('include_css')
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/common.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/board.css') }}">
@stop

@section('content')
<!-- Board start -->
<div id="board" class="container">
    @if($board->content_head)
        {!! $board->content_head !!}
    @endif

    @php
        $user = isset($user) ? $user : auth()->user();
    @endphp
    <div class="bd_head">
        <a href="{{ route('board.index', $board->table_name) }}">{{ $board->subject }}</a>
        @if($write->ca_name)
        <span class="ca_name">{{ $write->ca_name }}</span>
        @endif
    </div>
    <div class="bd_rd_head">
        <h1>{{ $write->subject }}</h1>
        <ul class="bd_rd_info">
            <li class="post_info">
            @if($board->use_sideview)
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                    @if(cache('config.join')->useMemberIcon && $write->iconPath)
                    <span class="tt_icon"><img src="{{ $write->iconPath }}" /></span>
                    @else
                    <i class="fa fa-user"></i>
                    @endif
                    <span class="tt_nick">{{ $write->name }}</span>
                </a>
                @component(getFrontSideview(), ['sideview' => 'board', 'board' => $board, 'write' => $write, 'category' => $currenctCategory])
                @endcomponent
            @else
                @if(cache('config.join')->useMemberIcon && $write->iconPath)
                <span class="tt_icon"><img src="{{ $write->iconPath }}" /></span>
                @else
                <i class="fa fa-user"></i>
                @endif
                <span class="tt_nick">{{ $write->name }}</span>
            @endif
            @if($board->use_ip_view) ({{ $write->ip }}) @endif
            </li>
            <li class="post_info"><i class="fa fa-clock-o"></i>@datetime($write->created_at)</li>
            <li class="post_info"><i class="fa fa-eye"></i>{{ $write->hit }}</li>
        </ul>
        <ul class="bd_rd_btn">
            <li class="depth2">
                <a href="{{ route('board.create', $board->table_name) }}" title="새글쓰기"><i class="fa fa-pencil"></i></a>
            </li>
            <li class="depth2">
                <a href="{{ route('board.index', $board->table_name). '?'. $request->server('QUERY_STRING') }}" title="목록으로"><i class="fa fa-list-ul"></i></a>
            </li>
            <li class="dropdown depth2">
                <a href="#" class="dropdown-toggle bd_rd_more" data-toggle="dropdown" role="button" aria-expanded="false" title="게시물관리">
                    <i class="fa fa-ellipsis-v"></i>
                </a>
                <ul class="dropdown-menu" role="menu">
                    @if( auth()->check() && ($user->id_hashkey == $write->user_id || !$write->user_id || $user->isBoardAdmin($board)) )
                        <li><a href="/bbs/{{ $board->table_name }}/edit/{{ $write->id. (Request::getQueryString() ? '?'.Request::getQueryString() : '') }}">수정</a></li>
                        <li>
                            <a href="{{ route('board.destroy', ['boardName' => $board->table_name, 'writeId' => $write->id]). (Request::getQueryString() ? '?'.Request::getQueryString() : '') }}"  onclick="del(this.href); return false;">
                                삭제
                            </a>
                        </li>
                    @endif
                    @if( (auth()->check() && $user->isBoardAdmin($board)) )
                        <li>
                            <a class="movePopup" href="{{ route('board.view.move', $board->table_name)}}?type=copy&amp;writeId={{ $write->id }}" target="move">
                                복사
                            </a>
                        </li>
                        <li>
                            <a class="movePopup" href="{{ route('board.view.move', $board->table_name)}}?type=move&amp;writeId={{ $write->id }}" target="move">
                                이동
                            </a>
                        </li>
                    @endif
                    <li><a href="{{ route('board.create.reply', ['board' => $board->table_name, 'writeId' => $write->id]). (Request::getQueryString() ? '?'.Request::getQueryString() : '') }}">답변</a></li>
                </ul>
            </li>
        </ul>
    </div>
    <div class="bd_rd">
    @if($write->link1 || $write->link2)
        @for($i=1; $i<=2; $i++)
        @if($write['link'.$i])
        <div class="bd_link">
            <i class="fa fa-link"></i>
            <a href="/bbs/{{ $board->table_name }}/views/{{ $write->id }}/link/{{ $i }}" target="_blank">{{ $write['link'. $i] }}</a>
            <span class="movecount">(연결된 횟수: {{ $write['link'. $i. '_hit'] }}회)</span>
        </div>
        @endif
        @endfor
    @endif

    @forelse($imgFiles as $imgFile)
        @php
            $divImage1 = explode('.', $imgFile['name']);
            $divImage2 = explode('_', $divImage1[0]);
            $realImageName = str_replace("thumb-", "", $divImage2[0]). '.'. last($divImage1);
        @endphp
        <div class="bd_rd">
          <a href="{{ route('image.original')}}/{{ $board->table_name }}?type=attach&amp;imageName={{ $realImageName }}"
             class="viewOriginalImage" width="{{ $imgFile[0] }}" height="{{ $imgFile[1] }}" target="viewImage">
                <img src="/storage/{{ $board->table_name. '/'. $imgFile['name'] }}" />
          </a>
        </div>
    @empty
    @endforelse

    <p>{!! $write->content !!}</p>

    @if(notNullCount($boardFiles) > 0)
        <div class="bd_file">
            <i class="fa fa-paperclip"></i>
            <span class="bd_title">첨부된 파일 {{ notNullCount($boardFiles) }}개</span>
            <ul class="bd_file_list" role="menu">
                @foreach($boardFiles as $file)
                <li>
                    <i class="fa fa-download"></i><a href="/bbs/{{ $board->table_name }}/views/{{ $write->id }}/download/{{ $file->board_file_no }}">{{ $file->source }}</a>
                    <span class="downcount">(다운로드 횟수: {{ $file->download }}회 / DATE : {{ $file->created_at }}) </span>
                </li>
                @endforeach
            </ul>
        </div>
    @endif
    @if($board->use_signature && $signature)
        <div class="bd_sign">
            {{ $signature }}
        </div>
    @endif
    </div>

    <div class="bd_rd_count">
        @auth
            <a href="{{ route('scrap.create') }}?boardName={{ $board->table_name }}&amp;writeId={{ $write->id }}" target="_blank" onclick="winScrap(this.href); return false;">
                <div class="countBtn">
                    <i class="fa fa-star" @if($scrap)style="color:#ff6699"@endif></i>스크랩
                </div>
            </a>
            @if($board->use_good)
            <a id="goodButton" href="/bbs/{{ $board->table_name }}/views/{{ $write->id }}/good">
                <div class="countBtn">
                    <i class="fa fa-thumbs-o-up"></i>추천
                    <strong>{{ $write->good }}</strong>
                    <span id="actGood" style="display: none;">이 글을 추천하셨습니다.</span> <!-- 메세지출력 -->
                </div>
            </a>
            @endif
            @if($board->use_nogood)
            <a id="noGoodButton" href="/bbs/{{ $board->table_name }}/views/{{ $write->id }}/nogood">
                <div class="countBtn">
                    <i class="fa fa-thumbs-o-down"></i>비추천
                    <strong>{{ $write->nogood }}</strong>
                    <span id="actNoGood" style="display: none;">이 글을 비추천하셨습니다.</span> <!-- 메세지출력 -->
                </div>
            </a>
            @endif
        @else
            @if($board->use_good)
            <span>
                <i class="fa fa-thumbs-o-up"></i>추천
                <strong>{{ $write->good }}</strong>
            </span>
            @endif
            @if($board->use_nogood)
            <span>
                <i class="fa fa-thumbs-o-down"></i>비추천
                <strong>{{ $write->nogood }}</strong>
            </span>
            @endif
        @endauth
    </div>

    <div class="bd_bna">
        <ul>
        @if($prevUrl != '')
            <li>
                <i class="fa fa-caret-up"></i>
                <span>이전글</span>
                <a href="{{ $prevUrl }}">{{ $prevSubject }}</a>
            </li>
        @endif
        @if($nextUrl != '')
            <li>
                <i class="fa fa-caret-down"></i>
                <span>다음글</span>
                <a href="{{ $nextUrl }}">{{ $nextSubject }}</a>
            </li>
        @endif
        </ul>
    </div>

    {{-- 댓글 --}}
    @if(view()->exists("themes.default.boards.$skin.comment"))
        @include("themes.default.boards.$skin.comment")
    @else
        @include("themes.default.boards.default.comment")
    @endif

    {{-- 전체 목록 보이기 설정시 --}}
    @if($board->use_list_view)
        @if(view()->exists("themes.default.boards.$skin.list"))
            @include("themes.default.boards.$skin.list")
        @else
            @include("themes.default.boards.default.list")
        @endif
    @endif

    @if($board->content_tail)
        {!! $board->content_tail !!}
    @endif
</div>

<script>
$(function() {
    $(".bd_title").click(function(){
        $(".bd_file_list").toggle();
    });

    $(".viewOriginalImage").click(function() {
        var width = $(this).attr('width');
        var height = $(this).attr('height');
        var top = (screen.availHeight-this.height) / 2;

        window.open(this.href, 'viewImage', 'location=yes,links=no,toolbar=no,left=0, top=' + top + ', width=' + width + ', height=' + height + ',resizable=yes,scrollbars=no,status=no');
        return false;
    });

    $(".movePopup").click(function() {
        window.open(this.href, 'move', 'left=50, top=50, width=500, height=550, scrollbars=1');
        return false;
    });

    // 추천, 비추천
    $("#goodButton, #noGoodButton").click(function() {
        var $tx;
        if(this.id == "goodButton") {
            $tx = $("#actGood");
        } else {
            $tx = $("#actNoGood");
        }

        excuteGood(this.href, $(this), $tx);
        return false;
    });

    // 이미지 리사이즈
    $('#board').viewimageresize();
});

// 추천, 비추천 ajax로 실행
function excuteGood(href, $el, $tx) {
    $.ajax({
        url: href,
        type: 'post',
        data: {
            'js': "on",
            '_token' : "{{ csrf_token() }}"
        },
        dataType: 'json',
        success: function(data) {
            if(data.error) {
                alert(data.error);
                return false;
            }

            if(data.count) {
                $el.find("strong").text(number_format(String(data.count)));
                if($tx.attr("id").search("NoGood") > -1) {
                    $tx.text("이 글을 비추천하셨습니다.");
                    $tx.fadeIn(200).delay(2500).fadeOut(200);
                } else {
                    $tx.text("이 글을 추천하셨습니다.");
                    $tx.fadeIn(200).delay(2500).fadeOut(200);
                }
            }
        },
    });
}
</script>
@stop
