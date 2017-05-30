@extends('theme')

@section('title')
    {{ $view->subject }} > {{ $board->subject }} | {{ App\Config::getConfig('config.homepage')->title }}
@endsection

@section('include_script')
    <script src="{{ asset('js/viewimageresize.js') }}"></script>
    <script src="{{ asset('js/common.js') }}"></script>
@endsection

@section('content')
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
                    @if(session()->get('admin') || auth()->user()->id == $view->user_id)
    	                <li><a href="/board/{{ $board->id }}/edit/{{ $view->id }}">수정</a></li>
    	                <li><a href="/board/{{ $board->id }}/delete/{{ $view->id }}" onclick="del(this.href); return false;">삭제</a></li>
                    @endif
                    @if(session()->get('admin'))
    	                <li>
                            <a class="movePopup" href="{{ route('board.view.move', $board->id)}}?type=copy&amp;writeId={{ $view->id }}" target="move">
                                복사
                            </a>
                        </li>
    	                <li>
                            <a class="movePopup" href="{{ route('board.view.move', $board->id)}}?type=move&amp;writeId={{ $view->id }}" target="move">
                                이동
                            </a>
                        </li>
                    @endif
	                <li><a href="{{ route('board.create.reply', ['boardId' => $board->id, 'writeId' => $view->id]) }}">답변</a></li>
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
              <a href="{{ route('image.original')}}/{{ $board->table_name }}?type=attach&amp;imageName={{str_replace("thumb-", "", $imgFile['name'])}}"
                 class="viewOriginalImage" width="{{ $imgFile[0] }}" height="{{ $imgFile[1] }}" target="viewImage">
                    <img src="/storage/{{ $board->table_name. '/'. $imgFile['name'] }}" />
              </a>
            </div>
        @endforeach
    @endif

	<div class="bd_rd">
        {!! $view->content !!}
	</div>

    @if($view->link1 || $view->link2 || $view->file > 0 || ($board->use_signature && auth()->user()->signature) )
        <div class="bd_add">
        @for($i=1; $i<=2; $i++)
            @if($view['link'.$i])
                <div class="bd_link">
                    <i class="fa fa-link"></i>
                    <a href="/board/{{ $board->id }}/view/{{ $view->id }}/link/{{ $i }}" target="_blank">{{ $view['link'. $i] }}</a>
                    <br>
                    <span class="movecount">{{ $view['link'. $i. '_hit'] }}회 연결</span>
                </div>
            @endif
        @endfor
        @if($view->file > 0)
            @foreach($boardFiles as $file)
                <div class="bd_file">
                    <i class="fa fa-download"></i>
                    <a href="/board/{{ $board->id }}/view/{{ $view->id }}/download/{{ $file->board_file_no }}">{{ $file->source }}</a>
                    <br>
                    <span class="downcount">{{ $file->download }}회 다운로드 DATE : {{ $file->created_at }}</span>
                </div>
            @endforeach
        @endif
        @if($board->use_signature)
            <div class="bd_sign">
                {{ $signature }}
            </div>
        @endif
        </div>
    @endif

	<div class="bd_rd_bt clearfix">
		<p class="pull-left bd_rd_cmthd">댓글 {{ count($comments) }}개</p>
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

    @if(count($comments) > 0)
    @foreach($comments as $comment)
	<section id="bd_rd_cmt">
		<article class="cmt">
            @if(strlen($comment->comment_reply) > 0) <!-- 답글일 경우 추가 -->
                <div class="cmt_reply pull-left">
                    @for($i=0; $i<strlen($comment->comment_reply); $i++)
                        &nbsp;&nbsp;
                    @endfor
                    <i class="fa fa-reply fa-rotate-180"></i>
                </div>
            @endif
			<div>
				<div class="clearfix">
					<ul class="bd_rd_cmt_info pull-left">
						<li><i class="fa fa-user"></i>
                        @if($board->use_sideview == 1)
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">{{ $comment->name }}</a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="#">자기소개</a></li>
                                <li><a href="#">전체게시물</a></li>
                                <li>
                                    <a href="/board/{{ $board->id }}?kind=user_id&amp;keyword={{ $comment->user_id }}">
                                        닉네임으로 검색
                                    </a>
                                </li>
                            </ul>
                        @else
                            {{ $comment->name }}
                        @endif
                        @if($board->use_ip) ({{ $comment->ip }}) @endif</li>
						<li><i class="fa fa-clock-o"></i>@datetime($comment->created_at)</li>
					</ul>

					<ul class="bd_rd_cmt_info pull-right">
						<li><a href="#" onclick="commentBox({{ $comment->id }}, 'c'); return false;">답변</a></li>
						<li><a href="#" onclick="commentBox({{ $comment->id }}, 'cu'); return false;">수정</a></li>
						<li><a href="#">삭제</a></li>
					</ul>
				</div>
				<div class="bd_rd_cmt_view">
					{!! $comment->content !!}
                    <input type="hidden" id="secretComment_{{ $comment->id }}" value="{{ $comment->option }}">
                    <textarea id="saveComment_{{ $comment->id }}" style="display:none">{!! $comment->content !!}</textarea>
				</div>
                <span id="reply_{{ $comment->id }}"></span><!-- 답변 -->
                <span id="edit_{{ $comment->id }}"></span><!-- 수정 -->
			</div>
		</article>
	</section>
    @endforeach
    @else
    <section id="bd_rd_cmt">
		<article class="cmt">
            <p>등록된 댓글이 없습니다.</p>
        </article>
    </section>
    @endif

    <aside id="commentWriteArea">
	<form class="cmt_write" id="commentForm" method="post" action="" onsubmit="return commentSubmit(this);" autocomplete="off">
        {{ csrf_field() }}
        <input type="hidden" name="writeId" value="{{ $view->id }}" />
        <input type="hidden" name="commentId" id="commentId" />
        <input type="hidden" name="_method" id="_method" />
		<div class="form-inline info_user">
            @if( is_null(auth()->user()) )  <!-- 비회원일경우 노출 -->
    			<div class="form-group">
    			    <label for="name"  class="sr-only">이름</label>
    			    <input type="text" class="form-control" id="name" name="name" placeholder="이름">
    			</div>

    			<div class="form-group">
    			    <label for="password" class="sr-only">비밀번호</label>
    			    <input type="password" class="form-control" id="password" name="password" placeholder="비밀번호">
    			</div>
            @endif

			<div class="form-group checkbox">
			    <label>
				   	<input type="checkbox" name="secret" id="secret" value="secret"><span>비밀글 사용</span>
				</label>
			</div>
		</div>

		<textarea class="form-control" rows="4" name="content" id="content" placeholder="댓글을 입력해 주세요."></textarea>

	    <div class="row clearfix">
	    	<!-- 리캡챠 -->

			<div class="pull-right col-md-3">
				<input type="submit" id="btnSubmit" class="btn btn-sir btn-block btn-lg" value="댓글등록" />
			</div>
		</div>
	</form>
    </aside>
</div>
@if($board->use_list_view)
    @include('board.list')
@endif

<script>
var saveBefore = '';
var saveHtml = document.getElementById('commentWriteArea').innerHTML;

function commentSubmit(form) {

    var subject = "";
    var content = "";

    $.ajax({
        url: '/ajax/filter',
        type: 'post',
        data: {
            '_token' : '{{ csrf_token() }}',
            'subject' : '',
            'content' : form.content.value
        },
        dataType: 'json',
        async: false,
        cache: false,
        success: function(data) {
            subject = data.subject;
            content = data.content;
        }, error: function(error) {
            alert(error);
        }
    });

    if(content) {
        alert("내용에 금지단어'" + content + "')가 포함되어 있습니다.");
        form.content.focus();
        return false;
    }

    document.getElementById("btnSubmit").disabled = "disabled";

    return true;

}

// 댓글의 답변, 수정 관련 조정 함수
function commentBox(commentId, work) {
    var el;
    // 댓글 아이디가 넘어오면 답변, 수정
    if (commentId) {
        if (work == 'c') {
            el = 'reply_' + commentId;
        } else {
            el = 'edit_' + commentId;
        }
    } else {
        el = 'commentWriteArea';
    }

    if (saveBefore != el) {
        if (saveBefore) {
            document.getElementById(saveBefore).style.display = 'none';
            document.getElementById(saveBefore).innerHTML = '';
        }

        document.getElementById(el).style.display = '';
        document.getElementById(el).innerHTML = saveHtml;
        // 댓글 수정
        if (work == 'cu') {
            document.getElementById('content').value = document.getElementById('saveComment_' + commentId).value;
            if (typeof char_count != 'undefined')
                check_byte('content', 'char_count');
            if (document.getElementById('secretComment_'+commentId).value) {
                document.getElementById('secret').checked = true;
            } else {
                document.getElementById('secret').checked = false;
            }
            document.getElementById('commentForm').action = "{{ route('board.comment.update', $board->id)}}";
            document.getElementById('_method').value = "PUT";

        } else {
            document.getElementById('commentForm').action = "{{ route('board.comment.store', $board->id)}}";
            document.getElementById('_method').value = "POST";
        }

        document.getElementById('commentId').value = commentId;
        // if(saveBefore) {
        //     $("#captcha_reload").trigger("click");
        // }

        saveBefore = el;
    }
}

// 댓글 입력폼이 보이도록 처리하기위해서 추가 (root님)
commentBox('', 'c');

$(function() {
    $('body').css('background', '#fff');

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

// 삭제 검사 확인
function del(href) {
    if(confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?")) {
        document.location.href = href;
    }
}
</script>
@endsection
