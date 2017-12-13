@extends("themes.". cache('config.theme')->name. ".layouts.". ($board->layout ? : 'basic'))

@section('title'){{ $board->subject }}게시판 | {{ cache("config.homepage")->title }}@endsection

@section('include_css')
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/common.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/board.css') }}">
@endsection

@section('include_script')
    @include("common.tinymce")
@endsection

@section('content')
<div id="board" class="container">
@if($errors->any())
<script>
    alert("{{ $errors->first() }}");
</script>
@endif

@php
    $useEditor = $board->use_dhtml_editor;
    $htmlUsable = $userLevel >= $board->html_level ? 1 : 0;
@endphp

@if($type=='update')
    <form role="form" id="fwrite" method="post" action={{ route('board.update', ['boardId'=>$board->table_name, 'writeId'=>$write->id]) }} enctype="multipart/form-data" @if(auth()->user() && auth()->user()->isBoardAdmin($board)) onsubmit="return writeSubmit('{{ $useEditor }}', '{{ $htmlUsable }}');" @endif>
        <input type="hidden" name="queryString" id="queryString" value="{{ Request::getQueryString() }}" />
        {{ method_field('put') }}
@else
    <form role="form" id="fwrite" method="post" action={{ route('board.store', $board->table_name) }} enctype="multipart/form-data" @if(auth()->guest() || !auth()->user()->isBoardAdmin($board)) onsubmit="return writeSubmit('{{ $useEditor }}', '{{ $htmlUsable }}');" @endif>
@endif
    <input type="hidden" name="type" id="type" value="{{ $type }}" />
    <input type="hidden" name="writeId" id="writeId" @if($type != 'create') value="{{ $write->id }}" @endif/>
    <input type="hidden" name="uid" id="uid" value="{{ str_replace("/", "-", substr(bcrypt(date('ymdHis', time())), 10, 60)) }}" />
    {{ csrf_field() }}
    @php
        $level = auth()->check() ? auth()->user()->level : 1;
    @endphp
    @if( ($type == 'create' && auth()->guest() )
        || ($type == 'update' && auth()->guest())
        || ($type == 'update' && !auth()->user()->isBoardAdmin($board) && $write->user_id != auth()->user()->id)
        || ($type == 'reply' && auth()->guest()) )
    <div class="nologin">
        <div class="form-group mb10 row @if($errors->get('name'))has-error @endif">
            <div class="col-sm-3">
                <label for="name" class="sr-only">이름</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="이름" @if($type == 'update') value="{{ $write->name }}"@else value="{{ old('name', '') }}" @endif required>
                @foreach ($errors->get('name') as $message)
                <span class="help-block">
                    <strong>{{ $message }}</strong>
                </span>
                @endforeach
            </div>
        </div>
        <div class="form-group mb10 row @if($errors->get('password'))has-error @endif">
            <div class="col-sm-4">
                <label for="password" class="sr-only">비밀번호</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="비밀번호" required>
                @foreach ($errors->get('password') as $message)
                <span class="help-block">
                    <strong>{{ $message }}</strong>
                </span>
                @endforeach
            </div>
        </div>
        <div class="form-group mb10 row @if($errors->get('email'))has-error @endif">
            <div class="col-sm-5">
                <label for="email" class="sr-only">이메일</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="이메일" @if($type=='update') value="{{ $write->email }}"@else value="{{ old('email', '') }}" @endif>
                @foreach ($errors->get('email') as $message)
                <span class="help-block">
                    <strong>{{ $message }}</strong>
                </span>
                @endforeach
            </div>
        </div>
        <div class="form-group mb10 row @if($errors->get('homepage'))has-error @endif">
            <div class="col-sm-5">
                <label for="homepage" class="sr-only">홈페이지</label>
                <input type="text" class="form-control" id="homepage" name="homepage" placeholder="홈페이지" @if($type=='update') value="{{ $write->homepage }}"@else value="{{ old('email', '') }}" @endif>
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
        <div class="col-sm-3">
            <select class="form-control" id="ca_name" name="ca_name" required>
                <option value>분류</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" @if( ($type != 'create' && $category == $write->ca_name) || ($type == 'create' && ($category == $currenctCategory || $category == old('ca_name')) ) ) selected @endif>
                        {{ $category }}
                    </option>
                @endforeach
                @if(!is_int(array_search('공지', $categories)) && auth()->user() && auth()->user()->isBoardAdmin($board))
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
        <div class="form-group mb10 col-sm-8 @if($errors->get('subject'))has-error @endif">
            <label for="" class="sr-only">게시물 작성</label>
            <input type="text" class="form-control" id="subject" name="subject" placeholder="게시물 제목" @if($type == 'create') value="{{ old('subject') }}"@else value="{{ $write->subject }}" @endif required>
            @foreach ($errors->get('subject') as $message)
            <span class="help-block">
                <strong>{{ $message }}</strong>
            </span>
            @endforeach
        </div>
        @auth
        <script src="{{ ver_asset('js/autosave.js') }}"></script>
        <div class="bd-save col-xs-4 dropdown">
            <a href="#" id="autosaveBtn" class="dropdown-toggle btn btn-sir" data-toggle="dropdown" role="button" aria-expanded="false">
                <i class="fa fa-archive"></i>
                <span style="margin-left: 5px" id="autosaveCount">({{ $autosaveCount }})</span>
            </a>
            <ul class="dropdown-menu" role="menu" id="autosavePop"></ul>
        </div>
        @endauth
    </div>

@if($board->use_dhtml_editor && $userLevel >= $board->html_level)
    <div style="border: 1px solid #ccc; background: #fff; min-height: 400px; border-radius: 4px; box-sizing: border-box; margin-bottom: 10px;" @if($errors->get('content')) class="has-error" @endif>
        <textarea class="editorArea" name="content" id="content">@if($type == 'update'){{ convertText($write->content, 0) }}@else{{ old('content', '') }}@endif</textarea>
    </div>
@else
@if(auth()->guest() || !auth()->user()->isSuperAdmin())
    @if($board->write_min || $board->write_max)
        <p id="charCountDesc">이 게시판은 최소 <strong>{{ $board->write_min }}</strong>글자 이상, 최대 <strong>{{ $board->write_max }}</strong>글자 이하까지 글을 쓰실 수 있습니다.</p>
    @endif
@endif
    <div style="border: 1px solid #ccc; background: #fff; min-height: 400px; border-radius: 4px; box-sizing: border-box; margin-bottom: 10px; padding: 2px;" @if($errors->get('content')) class="has-error" @endif>
        <textarea name="content" id="content" maxlength="65536" style="width:100%; min-height:400px; border:0;" required>@if($type == 'update'){{ convertText($write->content, 0) }}@else{{ old('content', '') }}@endif</textarea>
    </div>
@if(auth()->guest() || !auth()->user()->isSuperAdmin())
    @if($board->write_min || $board->write_max)
    <div id="charCountWrap">
        <span id="charCount">0</span>글자
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
                    <input type="text" class="form-control" id="link1" name="link1" placeholder="링크 1" @if($type == 'update')value="{{ $write->link1 }}"@else value="{{ old('link1', '') }}"@endif>
                </div>
                <div class="item">
                    <label for="link2" class="sr-only">링크 2</label>
                    <input type="text" class="form-control" id="link2" name="link2" placeholder="링크 2" @if($type == 'update')value="{{ $write->link2 }}"@else value="{{ old('link2', '') }}"@endif>
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
                @for($i=notNullCount($boardFiles); $i<$board->upload_count; $i++)
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
            @if(cache('config.email.default')->emailUse && $board->use_email && auth()->check())
            <label for="mail" class="checkbox-inline">
                <input type="checkbox" id="mail" name="mail" value="mail" checked> 답변메일받기
            </label>
            @endif
        </div>
        <div class="pull-right">
            <button type="button" class="btn btn-sir submitBtn">작성완료</button>
            <a href="{{ route("board.index", $board->table_name). (Request::getQueryString() ? '?'.Request::getQueryString() : '')}}" type="button" class="btn btn-default">취소</a>

            {{ fireEvent('captchaPlace') }}

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
$(function() {
    $(".link").click(function(){
        $(".link_list").toggle();
        $("#link1").focus();
    });

    $(".file").click(function(){
        $(".file_list").toggle();
    });

    {{-- 글자수 제한 --}}
    @if(!$board->use_dhtml_editor || $userLevel < $board->html_level)
    @if(auth()->guest() || !auth()->user()->isSuperAdmin())
    @if($board->write_min || $board->write_max)
    $("#content").on("keyup", function() {
        check_byte("content", "charCount");
    });
    @endif
    @endif
    @endif
});
</script>
@endsection
