@extends( 'layout.'. ($board->layout ? : cache('config.skin')->layout. '.basic') )

@section('title')
    {{ $board->subject }} 게시글 작성 | {{ Cache::get("config.homepage")->title }}
@endsection

@section('include_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/board.css') }}">
@endsection

@section('include_script')
    <script src="{{ asset('tinymce/tinymce.min.js') }}"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endsection

@section('content')
<!-- Board start -->
<div id="board" class="container">

    <!-- 게시글 작성 -->
    @if($type=='update')
        <form role="form" id="fwrite" method="post" action={{ route('board.update', ['boardId'=>$board->table_name, 'writeId'=>$write->id])}} enctype="multipart/form-data" @if(auth()->user() && auth()->user()->isBoardAdmin($board)) onsubmit="return writeSubmit();" @endif>
            {{ method_field('put') }}
    @else
        <form role="form" id="fwrite" method="post" action={{ route('board.store', $board->table_name) }} enctype="multipart/form-data" @if(auth()->guest() || !auth()->user()->isBoardAdmin($board)) onsubmit="return writeSubmit();" @endif>
    @endif
        <input type="hidden" name="type" id="type" value="{{ $type }}" />
        <input type="hidden" name="writeId" id="writeId" @if($type != 'create') value="{{ $write->id }}" @endif/>
        <input type="hidden" name="uid" id="uid" value="{{ str_replace("/", "-", substr(bcrypt(date('ymdHis', time())), 10, 60)) }}" />
        {{ csrf_field() }}
        @if( ($type == 'create' && auth()->guest() )
            || ($type == 'update' && auth()->guest())
            || ($type == 'update' && auth()->user()->isBoardAdmin($board) && $write->user_id != auth()->user()->id))
        <div class="nologin">
            <div class="form-group mb10 row @if($errors->get('name'))has-error @endif">
                <div class="col-xs-3">
                    <label for="name" class="sr-only">이름</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="이름" @if($type=='update') value={{ $write->name }}@else required @endif>
                    @foreach ($errors->get('name') as $message)
                    <span class="help-block">
                        <strong>{{ $message }}</strong>
                    </span>
                    @endforeach
                </div>
            </div>
            <div class="form-group mb10 row @if($errors->get('password'))has-error @endif">
                <div class="col-xs-4">
                    <label for="password" class="sr-only">비밀번호</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="비밀번호" @if($type!='update') required @endif>
                    @foreach ($errors->get('password') as $message)
                    <span class="help-block">
                        <strong>{{ $message }}</strong>
                    </span>
                    @endforeach
                </div>
            </div>
            <div class="form-group mb10 row @if($errors->get('email'))has-error @endif">
                <div class="col-xs-5">
                    <label for="email" class="sr-only">이메일</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="이메일" @if($type=='update') value="{{ $write->email }}" @endif>
                    @foreach ($errors->get('email') as $message)
                    <span class="help-block">
                        <strong>{{ $message }}</strong>
                    </span>
                    @endforeach
                </div>
            </div>
            <div class="form-group mb10 row @if($errors->get('homepage'))has-error @endif">
                <div class="col-xs-5">
                    <label for="homepage" class="sr-only">홈페이지</label>
                    <input type="text" class="form-control" id="homepage" name="homepage" placeholder="홈페이지" @if($type=='update') value="{{ $write->homepage }}" @endif>
                    @foreach ($errors->get('homepage') as $message)
                    <span class="help-block">
                        <strong>{{ $message }}</strong>
                    </span>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if($board->use_category)
        <div class="form-group mb10 row @if($errors->get('ca_name'))has-error @endif">
            <div class="col-xs-3">
                <select class="form-control" id="ca_name" name="ca_name" required>
                    <option value>분류</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" @if( ($type != 'create' && $category == $write->ca_name) || ($type == 'create' && $category == $currenctCategory ) ) selected @endif>
                            {{ $category }}
                        </option>
                    @endforeach
                    @if(auth()->user() && auth()->user()->isBoardAdmin($board))
                        <option value="공지">공지</option>
                    @endif
                </select>
            </div>
            @foreach ($errors->get('ca_name') as $message)
            <div>
                <span class="help-block">
                    <strong>{{ $message }}</strong>
                </span>
            </div>
            @endforeach
        </div>
        @endif

        <div class="row">
            <div class="form-group mb10 col-xs-8 @if($errors->get('subject'))has-error @endif">
                <label for="" class="sr-only">게시물 작성</label>
                <input type="text" class="form-control" id="subject" name="subject" placeholder="게시물 제목" @if($type != 'create') value="{{ $write->subject }}" @endif required>
                @foreach ($errors->get('subject') as $message)
                <span class="help-block">
                    <strong>{{ $message }}</strong>
                </span>
                @endforeach
            </div>
            @if(auth()->user())
                <script src="{{ asset('js/autosave.js') }}"></script>
                <div class="bd-save col-xs-4 dropdown">
                    <a href="#" id="autosaveBtn" class="dropdown-toggle btn btn-sir" data-toggle="dropdown" role="button" aria-expanded="false">
                        <i class="fa fa-archive"></i>
                        <span style="margin-left: 5px" id="autosaveCount">({{ $autosaveCount }})</span>
                    </a>
                    <ul class="dropdown-menu" role="menu" id="autosavePop"></ul>
                </div>
            @endif
        </div>

@if($board->use_dhtml_editor && $userLevel >= $board->html_level)
        {{-- 에디터 --}}
        <div style="border: 1px solid #ccc; background: #fff; min-height: 400px; border-radius: 4px; box-sizing: border-box; margin-bottom: 10px;" @if($errors->get('content')) class="has-error" @endif>
            <textarea class="editorArea" name="content" id="content">@if($type == 'update'){{ convertContent($write->content, 0) }}@endif</textarea>
        </div>
@else
    @if(auth()->guest() || !auth()->user()->isSuperAdmin())
        @if($board->write_min || $board->write_max)
            <p id="charCountDesc">이 게시판은 최소 <strong>{{ $board->write_min }}</strong>글자 이상, 최대 <strong>{{ $board->write_max }}</strong>글자 이하까지 글을 쓰실 수 있습니다.</p>
        @endif
    @endif
        <div style="border: 1px solid #ccc; background: #fff; min-height: 400px; border-radius: 4px; box-sizing: border-box; margin-bottom: 10px; padding: 2px;" @if($errors->get('content')) class="has-error" @endif>
            <textarea name="content" id="content" maxlength="65536" style="width:100%; min-height:400px; border:0;" required>@if($type == 'update'){{ convertContent($write->content, 0) }}@endif</textarea>
        </div>
    @if(auth()->guest() || !auth()->user()->isSuperAdmin())
        @if($board->write_min || $board->write_max)
        <div id="charCountWrap">
            <span id="charCount"></span>글자

        </div>
        @endif
    @endif
@endif
    @foreach($errors->get('content') as $message)
        <p><strong style="color:#a94442;">{{ $message }}</strong></p>
    @endforeach
        <div class="wt_more">
            <div class="add">
                <div class="link">
                    <i class="fa fa-link"></i>
                    <span class="bd_title">링크추가</span>
                </div>
                <div class="link_list @if($errors->get('link1') || $errors->get('link2'))has-error @endif" style="display: none;">
                    <div class="item">
                        <label for="link1" class="sr-only">링크 1</label>
                        <input type="text" class="form-control" id="link1" name="link1" placeholder="링크 1" @if($type == 'update')value="{{ $write->link1 }}"@endif>
                    </div>
                    <div class="item">
                        <label for="link2" class="sr-only">링크 2</label>
                        <input type="text" class="form-control" id="link2" name="link2" placeholder="링크 2" @if($type == 'update')value="{{ $write->link2 }}"@endif>
                    </div>
                    @foreach ($errors->get('link1') as $message)
                    <div>
                        <span class="help-block">
                            <strong>{{ $message }}</strong>
                        </span>
                    </div>
                    @endforeach
                    @foreach ($errors->get('link2') as $message)
                    <div>
                        <span class="help-block">
                            <strong>{{ $message }}</strong>
                        </span>
                    </div>
                    @endforeach
                </div>
                <div class="file">
                    <i class="fa fa-download"></i>
                    <span class="bd_title">파일추가</span>
                </div>
                <div class="file_list" @if($type=='create' || !isset($boardFiles) || !$boardFiles) style="display: none;" @endif>
                    <div class="item">
                        <label for="attach_file" class="sr-only">파일첨부</label>
                @if($type=='update')
                    @foreach($boardFiles as $boardFile)
                        <input type="file" id="attach_file{{ $loop->index }}" name="attach_file[]" placeholder="파일첨부" title="파일첨부 {{ $loop->index + 1 }} : 용량 {{ $board->upload_size }} 바이트 이하만 업로드 가능">
                        @if($board->use_file_content)
                        <input type="text" class="form-control" id="file_content" name="file_content[]" value="{{ $boardFile->content }}" title="파일 설명을 입력해 주세요." size="50" placeholder="파일 설명">
                        @endif
                        <input type="checkbox" id="{{ 'file_del'. $loop->index }}" name="file_del[{{ $loop->index }}]" value=1 />
                        <label for="{{ 'file_del'. $loop->index }}">{{ $boardFile->source.'('.$boardFile->filesize.') 파일 삭제' }}</label>
                    @endforeach
                    @for($i=count($boardFiles); $i<$board->upload_count; $i++)
                        <input type="file" id="attach_file{{ $i }}" name="attach_file[]" placeholder="파일첨부" title="파일첨부 {{ $i + 1 }} : 용량 {{ $board->upload_size }} 바이트 이하만 업로드 가능">
                        @if($board->use_file_content)
                        <input type="text" class="form-control" id="file_content" name="file_content[]" title="파일 설명을 입력해 주세요." size="50" placeholder="파일 설명">
                        @endif
                    @endfor
                @else
                    @for($i=0; $i<$board->upload_count; $i++)
                        <input type="file" id="attach_file{{ $i }}" name="attach_file[]" placeholder="파일첨부" title="파일첨부 {{ $i + 1 }} : 용량 {{ $board->upload_size }} 바이트 이하만 업로드 가능">
                        @if($board->use_file_content)
                        <input type="text" class="form-control" id="file_content" name="file_content[]" title="파일 설명을 입력해 주세요." size="50" placeholder="파일 설명">
                        @endif
                    @endfor
                @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="pull-left">
                @if(auth()->user() && auth()->user()->isBoardAdmin($board))
                <label for="notice" class="checkbox-inline">
                    <input type="checkbox" id="notice" name="notice" value="1" @if($type=='update' && strpos($board->notice, (string)$write->id) !== false) checked @endif> 공지
                </label>
                @endif
                @if($userLevel >= $board->html_level)
                @if($board->use_dhtml_editor)
                <input type="hidden" name="html" value="html1" />
                @else
                <label for="html" class="checkbox-inline">
                    <input type="checkbox" id="html" name="html" onclick="htmlAutoBr(this);" @if($type=='update') @if(strstr($write->option, 'html1')) checked value="html1" @elseif(strstr($write->option, 'html2')) checked value="html2" @endif @endif> html
                </label>
                @endif
                @endif
                @if($board->use_secret == 1 || auth()->user() && auth()->user()->isBoardAdmin($board))
                <label for="secret" class="checkbox-inline">
                    <input type="checkbox" id="secret" name="secret" value="secret" @if($type=='update' && strpos($write->option, 'secret') !== false) checked @endif> 비밀글
                </label>
                @elseif($board->use_secret == 2)
                <input type="hidden" name="secret" value="secret" />
                @endif
                @if(auth()->user() && $board->use_email)
                <label for="mail" class="checkbox-inline">
                    <input type="checkbox" id="mail" name="mail" value="mail" checked> 답변메일받기
                </label>
                @endif
            </div>
            <div class="pull-right">
                @if((auth()->user() && auth()->user()->isBoardAdmin($board)) || !$board->use_recaptcha)
                    <button type="submit" class="btn btn-sir">작성완료</button>
                @else
                    <!-- 리캡챠 -->
                    <div id='recaptcha' class="g-recaptcha"
                        data-sitekey="{{ cache('config.sns')->googleRecaptchaClient }}"
                        data-callback="onSubmit"
                        data-size="invisible" style="display:none">
                    </div>
                    <button type="button" class="btn btn-sir" onclick="validate();">작성완료</button>
                @endif
                <button type="button" class="btn btn-default" onclick="history.back();">취소</button>
            </div>
        </div>
    </form>
    <iframe id="formTarget" name="formTarget" style="display:none"></iframe>
    <form id="imageForm" action="{{ route('image.upload') }}" target="formTarget" method="post" enctype="multipart/form-data" style="width:0px;height:0;overflow:hidden">
        {{ csrf_field() }}
        <input type="file" name="image_file" id="image_file" onchange="$('#imageForm').submit(); this.value='';" style="display:none">
    </form>
</div>
<script>
tinymce.init({
    selector: '.editorArea',
    language: 'ko_KR',
    branding: false,
    theme: "modern",
    skin: "lightgray",
    height: 400,
    min_height: 400,
    min_width: 400,
    selection_toolbar: 'bold italic | quicklink h2 h3 blockquote',
    plugins: 'link,autolink,image,imagetools,textcolor,lists,pagebreak,table,save,insertdatetime,preview,media,searchreplace,print,contextmenu,directionality,fullscreen,noneditable,visualchars',
    toolbar: "nonbreaking undo redo | styleselect | forecolor bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table link customImage media",
    relative_urls: false,
    setup: function(editor) {
        editor.addButton('customImage', {
            text: '사진',
            icon: 'image',
            onclick: function () {
                window.open('{{ route('image.form') }}','tinymcePop','width=640, height=480');
            }
        });
    }
});

function htmlAutoBr(obj) {
    if (obj.checked) {
        var result = confirm("자동 줄바꿈을 하시겠습니까?\n\n자동 줄바꿈은 게시물 내용중 줄바뀐 곳을<br>태그로 변환하는 기능입니다.");
        if (result)
            obj.value = "html2";
        else
            obj.value = "html1";
    } else {
        obj.value = "";
    }
}
function onSubmit(token) {
    $("#fwrite").submit();
}
function validate(event) {
    if(writeSubmit()) {
        grecaptcha.execute();
    }
}
function writeSubmit() {
    var subject = "";
    var content = "";
    var contentData = "";
    var useEditor = {{ $board->use_dhtml_editor }};
    var htmlUsable = {{ $userLevel >= $board->html_level ? 1 : 0 }};
    if(useEditor == 1 && htmlUsable == 1) {
        contentData = tinymce.get('content').getContent();
    } else {
        contentData = $('#content').val();
    }

    $.ajax({
        url: '/ajax/filter/board',
        type: 'post',
        data: {
            '_token' : '{{ csrf_token() }}',
            'subject' : $('#subject').val(),
            'content' : contentData
        },
        dataType: 'json',
        async: false,
        cache: false,
        success: function(data) {
            subject = data.subject;
            content = data.content;
        }
    });

    if(subject) {
        alert("제목에 금지단어 (" + subject + ") 가 포함되어 있습니다.");
        $('#subject').focus();
        return false;
    }

    if(content) {
        alert("내용에 금지단어 (" + content + ") 가 포함되어 있습니다.");
        tinymce.get('content').focus();
        return false;
    }

    return true;
}

$(function() {
    $(".link").click(function(){
        $(".link_list").toggle();
        $("#link1").focus();
    });

    $(".file").click(function(){
        $(".file_list").toggle();
    });
});
</script>
{{-- 글자수 제한 --}}
@if(!$board->use_dhtml_editor || $userLevel < $board->html_level)
@if(auth()->guest() || !auth()->user()->isSuperAdmin())
@if($board->write_min || $board->write_max)
<script>
    $(function() {
        $("#content").on("keyup", function() {
            check_byte("content", "charCount");
        });
    });
</script>
@endif
@endif
@endif
@endsection
