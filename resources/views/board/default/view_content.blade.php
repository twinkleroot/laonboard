@php
    $user = isset($user) ? $user : auth()->user();
@endphp
<div class="bd_rd_head">
    <h1>{{ $write->subject }}</h1>
    <ul class="bd_rd_info">
        <li class="post_info">
        @unless($write->iconPath)
            <i class="fa fa-user"></i>
        @endunless
        @if($board->use_sideview)
        @auth
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                @if(cache('config.join')->useMemberIcon && $write->iconPath)
                <span class="tt_icon"><img src="{{ $write->iconPath }}" /></span>
                @endif
                <span class="tt_nick">{{ $write->name }}</span>
            </a>
            @component('sideview', ['sideview' => 'board', 'board' => $board, 'write' => $write, 'category' => $currenctCategory])
            @endcomponent
        @else
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">{{ $write->name }}</a>
            @component('sideview', ['sideview' => 'board', 'board' => $board, 'write' => $write, 'category' => $currenctCategory])
            @endcomponent
        @endauth
        @else
            @if(cache('config.join')->useMemberIcon && $write->iconPath)
            <span class="tt_icon"><img src="{{ $write->iconPath }}" /></span>
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
            <a href="{{ route('board.create', $board->table_name) }}"><i class="fa fa-pencil"></i></a>
        </li>
        <li class="depth2">
            <a href="{{ route('board.index', $board->table_name). '?'. $request->server('QUERY_STRING') }}"><i class="fa fa-list-ul"></i></a>
        </li>
        <li class="dropdown depth2">
            <a href="#" class="dropdown-toggle bd_rd_more" data-toggle="dropdown" role="button" aria-expanded="false">
                <i class="fa fa-ellipsis-v"></i>
            </a>
            <ul class="dropdown-menu" role="menu">
                @if( (auth()->check() && $user->id_hashkey == $write->user_id) || !$write->user_id || session()->get('admin') )
                    <li><a href="/bbs/{{ $board->table_name }}/edit/{{ $write->id }}">수정</a></li>
                    <li>
                        <a href="{{ route('board.destroy', ['boardName' => $board->table_name, 'writeId' => $write->id]) }}">
                            삭제
                        </a>
                    </li>
                @endif
                @if(session()->get('admin'))
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
                <li><a href="{{ route('board.create.reply', ['board' => $board->table_name, 'writeId' => $write->id]) }}">답변</a></li>
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

    <p>{!! urlAutoLink(clean($write->content)) !!}</p>

@if(count($boardFiles) > 0)
    <div class="bd_file">
        <i class="fa fa-paperclip"></i>
        <span class="bd_title">첨부된 파일 {{ count($boardFiles) }}개</span>
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

<!-- 스크랩/추천/비추천 -->
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

<section id="bd_rd_cmt">
@forelse($comments as $comment)
    <article class="cmt" id="comment{{ $comment->id }}">
        <div class="cmt_box @if(strlen($comment->comment_reply)>0) cmt_reply" style="padding-left: calc(25px * {{ strlen($comment->comment_reply) }}); @endif">
            <ul class="bd_rd_cmt_info">
                <li class="post_info cmt_nick">
                @unless($comment->iconPath)
                    <i class="fa fa-user"></i>
                @endunless
                @if($board->use_sideview)
                @auth
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                        @if(cache('config.join')->useMemberIcon && $comment->iconPath)
                        <span class="tt_icon"><img src="{{ $comment->iconPath }}" /></span> <!-- 아이콘 -->
                        @endif
                        <span class="tt_nick">{{ $comment->name }}</span> <!-- 닉네임 -->
                    </a>
                    @component('sideview', ['sideview' => 'board', 'board' => $board, 'write' => $comment, 'category' => $currenctCategory or ''])
                    @endcomponent
                @else
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">{{ $comment->name }}</a>
                    @component('sideview', ['sideview' => 'board', 'board' => $board, 'write' => $comment, 'category' => $currenctCategory or ''])
                    @endcomponent
                @endauth
                @else
                    @if(cache('config.join')->useMemberIcon && $comment->iconPath)
                    <span class="tt_icon"><img src="{{ $comment->iconPath }}" /></span> <!-- 아이콘 -->
                    @endif
                    <span class="tt_nick">{{ $comment->name }}</span> <!-- 닉네임 -->
                @endif
                @if($board->use_ip_view) {{ "({$comment->ip})" }} @endif
                </li>
                <li class="post_info"><i class="fa fa-clock-o"></i>@datetime($comment->created_at)</li>
            </ul>
            <ul class="bd_rd_cmt_ctr">
                @if($comment->isReply == 1)
                <li><a href="#" onclick="commentBox({{ $comment->id }}, 'c'); return false;">답변</a></li> @endif
                @if($comment->isEdit == 1)
                <li><a href="#" onclick="commentBox({{ $comment->id }}, 'cu'); return false;">수정</a></li> @endif
                @if($comment->isDelete == 1)
                <li>
                    <a href="{{ route('board.comment.destroy', ['boardName' => $board->table_name, 'writeId' => $write->id, 'commentId' => $comment->id]) }}">
                        삭제
                    </a>
                </li>
                @endif
            </ul>
            <div class="bd_rd_cmt_view">
                @if(str_contains($comment->option, 'secret'))
                <img src="/themes/default/images/icon_secret.gif"> <!-- 비밀 -->
                    @if(auth()->check() && ($user->isSuperAdmin() || $user->isBoardAdmin($board) || $user->isGroupAdmin($board->group)))
                    {!! urlAutoLink(clean($comment->content)) !!}
                @elseif(session()->get(session()->getId(). 'secret_board_'. $board->table_name. '_write_'. $comment->id))
                    {!! urlAutoLink(clean($comment->content)) !!}
                    @elseif(auth()->check() && $user->id == $comment->user_id)
                    {!! urlAutoLink(clean($comment->content)) !!}
                    @else
                    <a href="/password/type/secret?boardName={{ $board->table_name }}&writeId={{ $comment->id }}&nextUrl={{ route('board.view', [ 'boardName' => $board->table_name, 'writeId' => $comment->parent ]). '?'. Request::getQueryString(). '#comment'. $comment->id }}">댓글내용확인</a>
                    @endif
                @else
                {!! urlAutoLink(clean($comment->content)) !!}
                @endif
                <input type="hidden" id="secretComment_{{ $comment->id }}" value="{{ $comment->option }}">
                <textarea id="saveComment_{{ $comment->id }}" style="display:none">{{ $comment->content }}</textarea>
            </div>
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
<form class="cmt_write" id="commentForm" method="post" action="" autocomplete="off">
    {{ csrf_field() }}
    <input type="hidden" name="writeId" value="{{ $write->id }}" />
    <input type="hidden" name="commentId" id="commentId" />
    @if(isset($requestUri))
        <input type="hidden" name="requestUri" id="requestUri" value="{{ $requestUri }}"/>
    @endif
    <input type="hidden" name="_method" id="_method" />
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
            <div class="form-group checkbox">
                <label>
                    <input type="checkbox" name="secret" id="secret" value="secret"><span>비밀글 사용</span>
                </label>
            </div>
        </div>
    </article>
    @if($board->comment_min || $board->comment_max)
        <span id="charCount">0</span>글자
    @endif
    <textarea class="form-control" rows="4" name="content" id="content" @if($board->comment_min || $board->comment_max) onkeyup="check_byte('content', 'charCount');" @endif placeholder="댓글을 입력해 주세요." required></textarea>
    @foreach ($errors->get('content') as $message)
    <span class="help-block" style="color:#a94442;">
        <strong>{{ $message }}</strong>
    </span>
    @endforeach
    <script>
        $(document).on("keyup change", "textarea#content[maxlength]", function(){
            var str = $(this).val()
            var mx = parseInt($(this).attr("maxlength"))
            if (str.length > mx) {
                $(this).val(str.substr(0, mx));
                return false;
            }
        });
    </script>

    <div class="clearfix">
        <div class="pull-right">
            @if( ((auth()->check() && !session()->get('admin')) && $board->use_recaptcha) || !auth()->check())
            <input type="hidden" name="g-recaptcha-response" id="g-response" />
            <button type="button" class="btn btn-sir" onclick="validate();">댓글등록</button>
            @else
            <button type="submit" id="btnSubmit" class="btn btn-sir">댓글등록</button>
            @endif
        </div>
    </div>
</form>
</aside>
<!-- 리캡챠 -->
<div id='recaptcha' class="g-recaptcha"
    data-sitekey="{{ cache('config.sns')->googleRecaptchaClient }}"
    data-callback="onSubmit"
    data-size="invisible" style="display:none">
</div>

<script>
var saveBefore = '';
var saveHtml = document.getElementById('commentWriteArea').innerHTML;

function validate(event) {
    if(commentSubmit()) {
        grecaptcha.execute();
    }
}

function onSubmit(token) {
    $("#g-response").val(token);
    $("#commentForm").submit();
}

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
        async: true,
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
            document.getElementById('_method').value = "PUT";

        } else {
            document.getElementById('commentForm').action = "{{ route('board.comment.store', $board->table_name)}}";
            document.getElementById('_method').value = "POST";
        }

        document.getElementById('commentId').value = commentId;

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
