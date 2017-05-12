@extends('theme')

@section('title')
    {{ $view->subject }} > {{ $board->subject }} | {{ App\Config::getConfig('config.homepage')->title }}
@endsection

@section('include_script')
    <script src="{{ asset('js/viewimageresize.js') }}"></script>
    <script src="{{ asset('js/common.js') }}"></script>
@endsection

@section('content')
{{-- <body style="background: #fff"> --}}

<div id="header">
</div>
<!-- Board start -->
<div id="board" class="container">

	<!-- 게시글 조회 -->
	<div class="bd_rd_head">
		<h1>{{ $view->subject }}</h1>
		<ul class="bd_rd_info">
			<li><i class="fa fa-user"></i>{{ $view->name }} @if($board->use_ip) ({{ $view->ip }}) @endif</li>
			<li><i class="fa fa-clock-o"></i>@datetime($view->created_at)</li>
			<li><i class="fa fa-eye"></i>{{ $view->hit }}</li>
		</ul>
		<ul class="bd_rd_btn pull-right">
			<li class="dropdown">
				<a href="#" class="dropdown-toggle bd_rd_more" data-toggle="dropdown" role="button" aria-expanded="false">
					<i class="fa fa-ellipsis-v"></i>
				</a>
				<ul class="dropdown-menu" role="menu">
	                <li><a href="#">수정</a></li>
	                <li><a href="#">삭제</a></li>
	                <li><a href="#">복사</a></li>
	                <li><a href="#">이동</a></li>
	                <li><a href="#">답변</a></li>
	            </ul>
			</li>
			<li>
				<a href="{{ route('board.index', $board->id). '?'. $request->server('QUERY_STRING') }}"><i class="fa fa-list-ul"></i></a>
			</li>
			<li>
				<a href="{{ route('board.create', $board->id) }}"><i class="fa fa-pencil"></i></a>
			</li>
		</ul>
	</div>
    @if(!$board->use_list_view)
    <div class="bd_rd">
        @if($prevUrl != '') <a href="{{ $prevUrl }}">이전글</a> @endif
        @if($nextUrl != '') <a href="{{ $nextUrl }}">다음글</a> @endif
    </div>
    @endif
    @if(count($imgFiles) > 0)
        @foreach($imgFiles as $imgFile)
            <div class="bd_rd">
              <a href="{{route('board.viewImage',[
                    'boardId'=>$board->id,
                    'writeId'=>$view->id,
                    'imageName'=>str_replace("thumb-","",$imgFile['name'])
                  ])}}"
                 class="viewOriginalImage" width="{{ $imgFile[0] }}" height="{{ $imgFile[1] }}" target="viewImage">
                    <img src="/storage/{{ $board->table_name. '/'. $imgFile['name'] }}" />
              </a>
            </div>
        @endforeach
    @endif

	<div class="bd_rd">
        {!! $view->content !!}
	</div>

    @if($board->use_signature)
        <div class="bd_rd">
            {{ $signature }}
        </div>
    @endif
    @for($i=1; $i<=2; $i++)
        @if($view['link'.$i])
            <div class="bd_rd">
                <a href="/board/{{ $board->id }}/view/{{ $view->id }}/link/{{ $i }}" target="_blank">{{ $view['link'. $i] }}</a>
                <br>
                <span>{{ $view['link'. $i. '_hit'] }}회 연결</span>
            </div>
        @endif
    @endfor
    @if($view->file > 0)
        @foreach($boardFiles as $file)
            <div class="bd_rd">
                <a href="/board/{{ $board->id }}/view/{{ $view->id }}/download/{{ $file->board_file_no }}">{{ $file->source }}</a>
                <br>
                <span>{{ $file->download }}회 다운로드 DATE : {{ $file->created_at }}</span>
            </div>
        @endforeach
    @endif

	<div class="bd_rd_bt clearfix">
		<p class="pull-left bd_rd_cmthd">댓글 2개</p>
		<ul class="pull-right bd_rd_count">
			<li>
				<i class="fa fa-heart"></i>
				<span class="bd_rd_bt_txt">스크랩</span>
				<span class="bd_rd_bt_count">0</span>
			</li>
            @if($board->use_good)
        		<li>
                    <a id="goodButton" href="/board/{{ $board->id }}/view/{{ $view->id }}/good">
            			<i class="fa fa-thumbs-o-up"></i>
            			<span class="bd_rd_bt_txt">
                            추천
                            <strong class="bd_rd_bt_count">{{ $view->good }}</strong>
                        </span>
                    </a>
        		</li>
            @endif
            @if($board->use_nogood)
    			<li>
                    <a id="noGoodButton" href="/board/{{ $board->id }}/view/{{ $view->id }}/nogood">
        				<i class="fa fa-thumbs-o-down"></i>
        				<span class="bd_rd_bt_txt">
                            비추천
                            <strong class="bd_rd_bt_count">{{ $view->nogood }}</strong>
                        </span>
                    </a>
    			</li>
            @endif
		</ul>
    </div>
    <span id="actGood"></span>
    <span id="actNoGood"></span>

	<section id="bd_rd_cmt">
		<article class="cmt">
			<!-- 답글일 경우 추가
			<div class="cmt_reply pull-left">
				<i class="fa fa-reply fa-rotate-180"></i>
			</div>
			 답글일 경우 추가 END -->
			<div>
				<div class="clearfix">
					<ul class="bd_rd_cmt_info pull-left">
						<li><i class="fa fa-user"></i>최고관리자 (106.245.92.30)</li>
						<li><i class="fa fa-clock-o"></i>17-03-29 11:33</li>
					</ul>

					<ul class="bd_rd_cmt_info pull-right">
						<li>답글</li>
						<li>수정</li>
						<li>삭제</li>
					</ul>
				</div>
				<div class="bd_rd_cmt_view">
					<p>작성한 코멘트<br>작성한 코멘트<br>작성한 코멘트<br>작성한 코멘트<br>작성한 코멘트<br>작성한 코멘트</p>
				</div>
			</div>
		</article>
		<article class="cmt">
			<!-- 답글일 경우 추가 -->
			<div class="cmt_reply pull-left">
				<i class="fa fa-reply fa-rotate-180"></i>
			</div>
			<!-- 답글일 경우 추가 END -->
			<div>
				<div class="clearfix">
					<ul class="bd_rd_cmt_info pull-left">
						<li><i class="fa fa-user"></i>최고관리자 (106.245.92.30)</li>
						<li><i class="fa fa-clock-o"></i>17-03-29 11:33</li>
					</ul>

					<ul class="bd_rd_cmt_info pull-right">
						<li>답글</li>
						<li>수정</li>
						<li>삭제</li>
					</ul>
				</div>
				<div class="bd_rd_cmt_view">
					<p>작성한 코멘트<br>작성한 코멘트<br>작성한 코멘트<br>작성한 코멘트<br>작성한 코멘트<br>작성한 코멘트</p>
				</div>
			</div>
		</article>
	</section>

	<form class="cmt_write">
		<div class="form-inline info_user">
			<div class="form-group"> <!-- 비회원일경우 노출 -->
			    <label for="" class="sr-only">이름</label>
			    <input type="text" class="form-control" id="" placeholder="이름">
			</div>

			<div class="form-group">
			    <label for="" class="sr-only">비밀번호</label>
			    <input type="password" class="form-control" id="" placeholder="비밀번호">
			</div> <!-- 비회원일경우 노출 END -->

			<div class="form-group checkbox">
			    <label>
				   	<input type="checkbox"><span>비밀글 사용</span>
				</label>
			</div>
		</div>

		<textarea class="form-control" rows="4" placeholder="덧글을 입력해 주세요."></textarea>


	    <div class="row clearfix">
	    	<!-- 리캡챠 -->

			<div class="pull-right col-md-3">
				<button type="submit" class="btn btn-sir btn-block btn-lg">댓글등록</button>
			</div>
		</div>
	</form>
</div>
@if($board->use_list_view)
    @include('board.list')
@endif
<script>
    $(function(){
        $('body').css('background', '#fff');

        $(".viewOriginalImage").click(function() {
            var width = $(this).attr('width');
            var height = $(this).attr('height');
            var top = (screen.availHeight-this.height) / 2;

            window.open(this.href, 'viewImage', 'location=yes,links=no,toolbar=no,left=0, top=' + top + ', width=' + width + ', height=' + height + ',resizable=yes,scrollbars=no,status=no');
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

    function excuteGood(href, $el, $tx)
    {
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
@endsection
