<!-- 코멘트 -->
<div class="bd_rd_bt">
    <p class="bd_rd_cmthd">댓글 {{ notNullCount($comments) }}개</p>
</div>

<section id="bd_rd_cmt">
@forelse($comments as $comment)
    <article class="cmt" id="comment{{ $comment->id }}">
        <div class="cmt_box @if(strlen($comment->comment_reply)>0) cmt_reply" style="padding-left: calc(25px * {{ strlen($comment->comment_reply) }}); @endif">
            <ul class="bd_rd_cmt_info">
                <li class="post_info cmt_nick">
                @if($board->use_sideview)
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                        @if(cache('config.join')->useMemberIcon && $comment->iconPath)
                        <span class="tt_icon"><img src="{{ $comment->iconPath }}" /></span> <!-- 아이콘 -->
                        @else
                        <i class="fa fa-user"></i>
                        @endif
                        <span class="tt_nick">{{ $comment->name }}</span> <!-- 닉네임 -->
                    </a>
                    @component(getFrontSideview(), ['sideview' => 'board', 'board' => $board, 'write' => $comment, 'category' => $currenctCategory or ''])
                    @endcomponent
                @else
                    @if(cache('config.join')->useMemberIcon && $comment->iconPath)
                    <span class="tt_icon"><img src="{{ $comment->iconPath }}" /></span> <!-- 아이콘 -->
                    @else
                    <i class="fa fa-user"></i>
                    @endif
                    <span class="tt_nick">{{ $comment->name }}</span> <!-- 닉네임 -->
                @endif
                @if($board->use_ip_view) {{ "({$comment->ip})" }} @endif
                </li>
                <li class="post_info"><i class="fa fa-clock-o"></i>@datetime($comment->created_at)</li>
            </ul>
            <div class="bd_rd_cmt_view">
                @if(str_contains($comment->option, 'secret'))
                <img src="/themes/default/images/icon_secret.gif"> <!-- 비밀 -->
                    @if(auth()->check() && ($user->isSuperAdmin() || $user->isBoardAdmin($board) || $user->isGroupAdmin($board->group)))
                    {!! $comment->content !!}
                @elseif(session()->get(session()->getId(). 'secret_board_'. $board->table_name. '_write_'. $comment->id))
                    {!! $comment->content !!}
                    @elseif(auth()->check() && $user->id == $comment->user_id)
                    {!! $comment->content !!}
                    @else
                    <a href="/password/type/secret?boardName={{ $board->table_name }}&writeId={{ $comment->id }}&nextUrl={{ route('board.view', [ 'boardName' => $board->table_name, 'writeId' => $comment->parent ]). '?'. Request::getQueryString(). '#comment'. $comment->id }}">댓글내용확인</a>
                    @endif
                @else
                {!! $comment->content !!}
                @endif
                <input type="hidden" id="secretComment_{{ $comment->id }}" value="{{ $comment->option }}">
                <textarea id="saveComment_{{ $comment->id }}" style="display:none">{{ strip_tags($comment->content) }}</textarea>
            </div>
            <ul class="bd_rd_cmt_ctr">
                @if($comment->isReply == 1)
                <li><a href="#" onclick="commentBox({{ $comment->id }}, 'c'); return false;">답변</a></li> @endif
                @if($comment->isEdit == 1)
                <li><a href="#" onclick="commentBox({{ $comment->id }}, 'cu'); return false;">수정</a></li> @endif
                @if($comment->isDelete == 1)
                <li>
                    <a href="{{ route('board.comment.destroy', ['boardName' => $board->table_name, 'writeId' => $write->id, 'commentId' => $comment->id]) }}" onclick="del(this.href); return false;">
                        삭제
                    </a>
                </li>
                @endif
            </ul>
            <span id="reply_{{ $comment->id }}"></span><!-- 답변 -->
            <span id="edit_{{ $comment->id }}"></span><!-- 수정 -->
        </div>
    </article>
@empty
    <article class="cmt">
        <p>등록된 댓글이 없습니다.</p>
    </article>
@endforelse
</section>
<aside id="commentWriteArea">
<form class="cmt_write" id="commentForm" method="post" action="" autocomplete="off" @if(auth()->user() && auth()->user()->isBoardAdmin($board)) onsubmit="return commentSubmit();" @endif>
    {{ csrf_field() }}
    <input type="hidden" name="writeId" value="{{ $write->id }}" />
    <input type="hidden" name="commentId" id="commentId" />
    @if(isset($requestUri))
        <input type="hidden" name="requestUri" id="requestUri" value="{{ $requestUri }}"/>
    @endif
    <input type="hidden" name="_method" id="commentMethod" />

    <article id="comment_box">
        <div class="form-inline info_user">
            @guest  <!-- 비회원일경우 노출 -->
            <div class="form-group @if($errors->get('password'))has-error @endif">
                <label for="userName" class="sr-only">이름</label>
                <input type="text" class="form-control" id="userName" name="userName" placeholder="이름">
                @foreach ($errors->get('userName') as $message)
                <span class="help-block">
                    <strong>{{ $message }}</strong>
                </span>
                @endforeach
            </div>

            <div class="form-group @if($errors->get('password'))has-error @endif">
                <label for="password" class="sr-only">비밀번호</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="비밀번호">
                @foreach ($errors->get('password') as $message)
                <span class="help-block">
                    <strong>{{ $message }}</strong>
                </span>
                @endforeach
            </div>
            @endguest
            <div class="cmt_checkbox">
                <label>
                    <input type="checkbox" name="secret" id="secret" value="secret"><span>비밀글 사용</span>
                </label>
            </div>
            @if($board->comment_min || $board->comment_max)
            <div class="cmt_character">
                <span id="charCount">0</span>글자
            </div>
            @endif
        </div>
    </article>
    <textarea class="form-control" rows="4" name="content" id="content" @if($board->comment_min || $board->comment_max) onkeyup="check_byte('content', 'charCount');" @endif placeholder="댓글을 입력해 주세요." required></textarea>
    @foreach ($errors->get('content') as $message)
    <span class="help-block" style="color:#a94442;">
        <strong>{{ $message }}</strong>
    </span>
    @endforeach

    <div class="clearfix">
        <div class="pull-right">
            <button type="button" class="btn btn-sir submitBtn">댓글등록</button>

            {{ fireEvent('captchaPlace') }}

        </div>
    </div>
</form>
</aside>

<script>
$(document).on("keyup change", "textarea#content[maxlength]", function(){
    var str = $(this).val()
    var mx = parseInt($(this).attr("maxlength"))
    if (str.length > mx) {
        $(this).val(str.substr(0, mx));
        return false;
    }
});

function commentSubmit() {
    var subject = "";
    var content = "";

    $.ajax({
        url: '/ajax/filter/board',
        type: 'post',
        data: {
            '_token' : '{{ csrf_token() }}',
            'subject' : '',
            'content' : $('#content').val()
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
        $('#content').focus();
        return false;
    }

    return true;
}

var saveBefore = '';
var saveHtml = document.getElementById('commentWriteArea').innerHTML;

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
            document.getElementById('commentForm').action = "{{ route('board.comment.update', $board->table_name)}}";
            document.getElementById('commentMethod').value = "PUT";

        } else {
            document.getElementById('commentForm').action = "{{ route('board.comment.store', $board->table_name)}}";
            document.getElementById('commentMethod').value = "POST";
        }

        document.getElementById('commentId').value = commentId;

        saveBefore = el;
    }
}

// 댓글 입력폼이 보이도록 처리하기위해서 추가 (root님)
commentBox('', 'c');
</script>
