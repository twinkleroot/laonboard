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
                @if(!$view->user_id || session()->get('admin') || ( auth()->user() && auth()->user()->id == $view->user_id ) )
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
<div class="bd_rd">
@if($view->link1 || $view->link2)
    @for($i=1; $i<=2; $i++)
        @if($view['link'.$i])
            <div class="bd_link">
                <i class="fa fa-link"></i>
                <a href="/board/{{ $board->id }}/view/{{ $view->id }}/link/{{ $i }}" target="_blank">{{ $view['link'. $i] }}</a>
                <span class="movecount">(연결된 횟수: {{ $view['link'. $i. '_hit'] }}회)</span>
            </div>
        @endif
    @endfor
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

<p>{!! $view->content !!}</p>

@if($view->file > 0)
        <div class="bd_file">
            <i class="fa fa-paperclip"></i>
            <span class="bd_title">첨부된 파일 {{ count($boardFiles) }}개</span>
            @foreach($boardFiles as $file)
            <ul class="bd_file_list" role="menu">
                <li>
                    <i class="fa fa-download"></i><a href="/board/{{ $board->id }}/view/{{ $view->id }}/download/{{ $file->board_file_no }}">{{ $file->source }}</a>
                    <span class="downcount">(다운로드 횟수: {{ $file->download }}회 / DATE : {{ $file->created_at }}) </span>
                </li>
            </ul>
            @endforeach
        </div>
@endif
@if($board->use_signature && $signature)
    <div class="bd_sign">
        {{ $signature }}
    </div>
@endif
</div>

<!-- 스크랩/추천/비추천 -->
<div class="bd_rd_count">
    @if( !is_null(auth()->user()) )
        <a href="{{ route('scrap.create') }}?boardId={{ $board->id }}&amp;writeId={{ $view->id }}" target="_blank" onclick="winScrap(this.href); return false;">
            <span>
                <i class="fa fa-star"></i>스크랩
            </span>
        </a>
        @if($board->use_good)
        <a id="goodButton" href="/board/{{ $board->id }}/view/{{ $view->id }}/good">
            <span>
                <i class="fa fa-thumbs-o-up"></i>추천
                <strong>{{ $view->good }}</strong>
                <span id="actGood" style="display: none;">이 글을 추천하셨습니다.</span> <!-- 메세지출력 -->
            </span>
        </a>
        @endif
        @if($board->use_nogood)
        <a id="noGoodButton" href="/board/{{ $board->id }}/view/{{ $view->id }}/nogood">
            <span>
                <i class="fa fa-thumbs-o-down"></i>비추천
                <strong>{{ $view->nogood }}</strong>
                <span id="actNoGood" style="display: none;">이 글을 비추천하셨습니다.</span> <!-- 메세지출력 -->
            </span>
        </a>
        @endif
    @else
        @if($board->use_good)
        <span>
            <i class="fa fa-thumbs-o-up"></i>추천
            <strong>{{ $view->good }}</strong>
        </span>
        @endif
        @if($board->use_nogood)
        <span>
            <i class="fa fa-thumbs-o-down"></i>비추천
            <strong>{{ $view->nogood }}</strong>
        </span>
        @endif
    @endif
</div>

<!-- 이전글/다음글 -->
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

<!-- 코멘트 -->
<div class="bd_rd_bt">
    <p class="bd_rd_cmthd">댓글 {{ count($comments) }}개</p>
</div>

@if(count($comments) > 0)
<section id="bd_rd_cmt">
    @foreach($comments as $comment)
    <article class="cmt" id="comment{{ $comment->id }}">
        <div class="cmt_box @if(strlen($comment->comment_reply)>0) cmt_reply" style="padding-left: calc(25px * {{ strlen($comment->comment_reply) }}); @endif">
            <ul class="bd_rd_cmt_info">
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
            <ul class="bd_rd_cmt_ctr">
                @if($comment->isReply == 1) <li><a href="#" onclick="commentBox({{ $comment->id }}, 'c'); return false;">답변</a></li> @endif
                @if($comment->isEdit == 1) <li><a href="#" onclick="commentBox({{ $comment->id }}, 'cu'); return false;">수정</a></li> @endif
                @if($comment->isDelete == 1) <li><a href="{{ route('board.comment.destroy', ['boardId' => $board->id, 'writeId' => $view->id, 'commentId' => $comment->id])}}" onclick="return commentDelete();">삭제</a></li> @endif
            </ul>
            <div class="bd_rd_cmt_view">
                {!! $comment->content !!}
                <input type="hidden" id="secretComment_{{ $comment->id }}" value="{{ $comment->option }}">
                <textarea id="saveComment_{{ $comment->id }}" style="display:none">{!! $comment->content !!}</textarea>
            </div>
            <span id="reply_{{ $comment->id }}"></span><!-- 답변 -->
            <span id="edit_{{ $comment->id }}"></span><!-- 수정 -->
        </div>
    </article>
    @endforeach
</section>
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
    @if(isset($requestUri))
        <input type="hidden" name="requestUri" id="requestUri" value="{{ $requestUri }}"/>
    @endif
    <input type="hidden" name="_method" id="_method" />

    @if( auth()->guest() )  <!-- 비회원일경우 노출 -->
    <article id="comment_box">
        <div class="form-inline info_user">
            <div class="form-group">
                <label for="userName" class="sr-only">이름</label>
                <input type="text" class="form-control" id="userName" name="userName" placeholder="이름">
            </div>

            <div class="form-group">
                <label for="password" class="sr-only">비밀번호</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="비밀번호">
            </div>
        </div>
    </article>
    @endif
    <div class="form-inline info_user">
        <div class="form-group checkbox">
            <label>
                <input type="checkbox" name="secret" id="secret" value="secret"><span>비밀글 사용</span>
            </label>
        </div>
    </div>
    @if($board->comment_min || $board->comment_max)
        <span id="charCount"></span>글자
    @endif
    <textarea class="form-control" rows="4" name="content" id="content" @if($board->comment_min || $board->comment_max) onkeyup="check_byte('content', 'charCount');" @endif placeholder="댓글을 입력해 주세요."></textarea>

    <script>
        $(document).on( "keyup change", "textarea#content[maxlength]", function(){
            var str = $(this).val()
            var mx = parseInt($(this).attr("maxlength"))
            if (str.length > mx) {
                $(this).val(str.substr(0, mx));
                return false;
            }
        });
    </script>

    <div class="row clearfix">
        <div class="pull-right col-md-3">
            @if(auth()->user() && auth()->user()->isAdmin())
                <button type="submit" id="btnSubmit" class="btn btn-sir btn-block btn-lg">댓글등록</button>
            @elseif( (auth()->user() && $board->use_recaptcha) || auth()->guest())
                <!-- 리캡챠 -->
                <div id='recaptcha' class="g-recaptcha"
                    data-sitekey="{{ env('GOOGLE_INVISIBLE_RECAPTCHA_KEY') }}"
                    data-callback="onSubmit"
                    data-size="invisible" style="display:none">
                </div>
                <button type="button" class="btn btn-sir btn-block btn-lg" onclick="validate();">댓글등록</button>
            @else
                <button type="submit" id="btnSubmit" class="btn btn-sir btn-block btn-lg">댓글등록</button>
            @endif
        </div>
    </div>

</form>
</aside>

<script>
var saveBefore = '';
var saveHtml = document.getElementById('commentWriteArea').innerHTML;

function validate(event) {
	grecaptcha.execute();
}

function onSubmit(token) {
	$("#commentForm").submit();
}

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
        alert("내용에 금지단어 (" + content + ") 가 포함되어 있습니다.");
        form.content.focus();
        return false;
    }

    // 양쪽 공백 없애기
    var pattern = /(^\s*)|(\s*$)/g; // \s 공백 문자
    document.getElementById('content').value = document.getElementById('content').value.replace(pattern, "");

    var minComment = parseInt('{{ $board->comment_min }}');
    var maxComment = parseInt('{{ $board->commnet_max }}');
    if (minComment > 0 || maxComment > 0) {
        check_byte('content', 'charCount');
        var cnt = parseInt(document.getElementById('charCount').innerHTML);
        if (minComment > 0 && minComment > cnt) {
            alert("댓글은 " + minComment + "글자 이상 쓰셔야 합니다.");
            return false;
        } else if (maxComment > 0 && maxComment < cnt) {
            alert("댓글은 " + maxComment + "글자 이하로 쓰셔야 합니다.");
            return false;
        }
    } else if (!document.getElementById('content').value) {
        alert("댓글을 입력하여 주십시오.");
        return false;
    }

    if (typeof(form.userName) != 'undefined') {
        console.log(form.userName);
        console.log(typeof(form.userName));
        console.log(form.userName.value);
        form.userName.value = form.userName.value.replace(pattern, "");
        if (form.userName.value == '') {
            alert('이름이 입력되지 않았습니다.');
            form.userName.focus();
            return false;
        }
    }

    if (typeof(form.password) != 'undefined') {
        form.password.value = form.password.value.replace(pattern, "");
        if (form.password.value == '') {
            alert('비밀번호가 입력되지 않았습니다.');
            form.password.focus();
            return false;
        }
    }

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
            if (typeof charCount != 'undefined')
                check_byte('content', 'charCount');
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

        if(saveBefore) {
            grecaptcha.reset(grecaptcha.render(document.getElementById('recaptcha_zone'), {
              'sitekey' : '6LcKohkUAAAAANcgIst0HFMMT81Wq5HIxpiHhXGZ'
            }));
        }

        saveBefore = el;
    }
}

// 댓글 입력폼이 보이도록 처리하기위해서 추가 (root님)
commentBox('', 'c');
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

// 댓글 삭제 확인
function commentDelete()
{
    return confirm("이 댓글을 삭제하시겠습니까?");
}
</script>
