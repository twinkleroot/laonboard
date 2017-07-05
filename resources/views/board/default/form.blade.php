@extends('layouts.'. ($board->layout ? : 'default.basic'))

@section('title')
    {{ $board->subject }} 게시글 작성 | {{ Cache::get("config.homepage")->title }}
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
        <form role="form" id="fwrite" method="post" action={{ route('board.update', ['boardId'=>$board->id, 'writeId'=>$write->id])}} enctype="multipart/form-data">
            {{ method_field('put') }}
    @else
        <form role="form" id="fwrite" method="post" action={{ route('board.store', $board->id) }} enctype="multipart/form-data">
    @endif
        <input type="hidden" name="type" id="type" value="{{ $type }}" />
        <input type="hidden" name="writeId" id="writeId" @if($type != 'create') value="{{ $write->id }}" @endif/>
        <input type="hidden" name="uid" id="uid" value="{{ str_replace("/", "-", substr(bcrypt(date('ymdHis', time())), 10, 60)) }}" />
        {{ csrf_field() }}
        @if( ($type == 'create' && is_null(auth()->user()) )
            || ($type == 'update' && session()->get('admin') && $write->user_id != auth()->user()->id) )
        <div class="nologin">
    		<div class="form-group mb10 row">
    			<div class="col-xs-3">
    				<label for="name" class="sr-only">이름</label>
    				<input type="text" class="form-control" id="name" name="name" placeholder="이름" @if($type=='update') value={{ $write->name }}@else required @endif>
    			</div>
    		</div>
    		<div class="form-group mb10 row">
    			<div class="col-xs-4">
    				<label for="password" class="sr-only">비밀번호</label>
    				<input type="password" class="form-control" id="password" name="password" placeholder="비밀번호" @if($type!='update') required @endif>
    			</div>
    		</div>
    		<div class="form-group mb10 row">
    			<div class="col-xs-5">
    				<label for="email" class="sr-only">이메일</label>
    				<input type="email" class="form-control" id="email" name="email" placeholder="이메일" @if($type=='update') value="{{ $write->email }}" @endif>
    			</div>
    		</div>
    		<div class="form-group mb10 row">
    			<div class="col-xs-5">
    				<label for="homepage" class="sr-only">홈페이지</label>
    				<input type="text" class="form-control" id="homepage" name="homepage" placeholder="홈페이지" @if($type=='update') value="{{ $write->homepage }}" @endif>
    			</div>
    		</div>
    	</div>
        @endif

        @if($board->use_category == 1)
            <div class="form-group mb10 row">
                <div class="col-xs-3">
                    <select class="form-control" name="ca_name" required>
                        <option value>분류</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" @if( ($type == 'update' && $category == $write->ca_name) || ($type == 'create' && $category == $currenctCategory ) ) selected @endif>
                                {{ $category }}
                            </option>
                        @endforeach
                        @if(session()->get('admin'))
                            <option value="공지">공지</option>
                        @endif
                    </select>
                </div>
            </div>
        @endif

    	<div class="row">
    		<div class="form-group mb10 col-xs-8">
    		    <label for="" class="sr-only">게시물 작성</label>
    		    <input type="text" class="form-control" id="subject" name="subject" placeholder="게시물 제목" @if($type != 'create') value="{{ $write->subject}}" @endif required>
    		</div>
            @if( !is_null(auth()->user()) )
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

        @if($board->use_dhtml_editor == 1)
            {{-- 에디터 --}}
            <div class="mb10" style="border: 1px solid #ccc; background: #fff; min-height: 400px; border-radius: 4px; box-sizing: border-box;">
                <textarea name="content" id="editorArea">@if($type == 'update'){{ $write->content }}@endif</textarea>
            </div>
        @else
        	<div class="mb10" style="border: 1px solid #ccc; background: #fff; min-height: 400px; padding: 20px; border-radius: 4px; box-sizing: border-box;">
                <textarea name="content" maxlength="65536" style="width:100%; min-height:400px" required>@if($type == 'update'){{ $write->content }}@endif</textarea>
        	</div>
        @endif

    	<div class="row">
    		<div class="form-group mb10 col-xs-8">
    		    <label for="link1" class="sr-only">링크 1</label>
    		    <input type="url" class="form-control" id="link1" name="link1" placeholder="링크 1" @if($type == 'update')value={{ $write->link1 }}@endif>
    		</div>

    		<div class="form-group mb10 col-xs-8">
    		    <label for="link2" class="sr-only">링크 2</label>
    		    <input type="url" class="form-control" id="link2" name="link2" placeholder="링크 2" @if($type == 'update')value={{ $write->link2 }}@endif>
    		</div>

    		<div class="form-group mb10 col-xs-5">
                @if($type=='update')
                    @foreach($boardFiles as $boardFile)
                        <label for="attach_file" class="sr-only">파일첨부</label>
                        <input type="file" id="attach_file" name="attach_file[]" placeholder="파일첨부"
                        title="파일첨부 {{ $loop->index + 1 }} : 용량 {{ $board->upload_size }} 바이트 이하만 업로드 가능">
                        @if($board->use_file_content)
                            <input type="text" class="form-control" id="file_content" name="file_content[]"
                            value="{{ $boardFile->content }}" title="파일 설명을 입력해 주세요." size="50" placeholder="파일 설명">
                        @endif
                        <input type="checkbox" id="{{ 'file_del'. $loop->index }}" name="file_del[{{ $loop->index }}]" value=1 />
                        <label for="{{ 'file_del'. $loop->index }}">{{ $boardFile->source.'('.$boardFile->filesize.') 파일 삭제' }}</label>
                    @endforeach
                    @for($i=0; $i<$board->upload_count-count($boardFiles); $i++)
                        <label for="attach_file" class="sr-only">파일첨부</label>
                        <input type="file" id="attach_file" name="attach_file[]" placeholder="파일첨부"
                        title="파일첨부 {{ $i + 1 }} : 용량 {{ $board->upload_size }} 바이트 이하만 업로드 가능">
                        @if($board->use_file_content)
                            <input type="text" class="form-control" id="file_content" name="file_content[]"
                            title="파일 설명을 입력해 주세요." size="50" placeholder="파일 설명">
                        @endif
                    @endfor
                @else
                    @for($i=0; $i<$board->upload_count; $i++)
                        <label for="attach_file" class="sr-only">파일첨부</label>
                        <input type="file" id="attach_file" name="attach_file[]" placeholder="파일첨부"
                        title="파일첨부 {{ $i + 1 }} : 용량 {{ $board->upload_size }} 바이트 이하만 업로드 가능">
                        @if($board->use_file_content)
                            <input type="text" class="form-control" id="file_content" name="file_content[]"
                            title="파일 설명을 입력해 주세요." size="50" placeholder="파일 설명">
                        @endif
                    @endfor
                @endif
    		</div>
    	</div>

    	<div class="clearfix">
    		<div class="pull-left">
                @if(session()->get('admin'))
        			<label for="notice" class="checkbox-inline">
        				<input type="checkbox" id="notice" name="notice" value="1" @if($type=='update' && strpos($board->notice, (string)$write->id) !== false) checked @endif> 공지
        			</label>
                @endif
                @if(!$board->use_dhtml_editor)
        			<label for="html" class="checkbox-inline">
        				<input type="checkbox" id="html" name="html" onclick="htmlAutoBr(this);" value="" @if($type=='update' && strpos($write->option, 'html') !== false) checked @endif> html
        			</label>
                @else
                    <input type="hidden" name="html" value="html1" />
                @endif
                @if($board->use_secret)
                    <label for="secret" class="checkbox-inline">
        				<input type="checkbox" id="secret" name="secret" value="secret" @if($type=='update' && strpos($write->option, 'secret') !== false) checked @endif> 비밀글
        			</label>
                @endif
                @if($board->use_email)
        			<label for="mail" class="checkbox-inline">
        				<input type="checkbox" id="mail" name="mail" value="mail"> 답변메일받기
        			</label>
                @endif
    		</div>
            <div class="pull-right">
                @if(session()->get('admin'))
                    <button type="submit" class="btn btn-sir">작성완료</button>
                @elseif( ($type == 'create' && auth()->guest() ) || ($type == 'create' && $board->use_recaptcha) || ($type == 'update' && !session()->get('admin') && $write->user_id != auth()->user()->id) )
                    <!-- 리캡챠 -->
                	<div id='recaptcha' class="g-recaptcha"
                		data-sitekey="{{ env('GOOGLE_INVISIBLE_RECAPTCHA_KEY') }}"
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
    selector: '#editorArea',
    language: 'ko_KR',
    branding: false,
    theme: "modern",
    skin: "lightgray",
    height: 400,
    min_height: 400,
    min_width: 400,
    selection_toolbar: 'bold italic | quicklink h2 h3 blockquote',
    plugins: 'link,autolink,image,imagetools,textcolor,lists,pagebreak,table,save,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,code',
    toolbar: "undo redo | styleselect | forecolor bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table link customImage media code",
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
	grecaptcha.execute();
}
</script>
@endsection
