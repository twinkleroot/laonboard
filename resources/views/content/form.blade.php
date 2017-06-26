@extends('admin.admin')

@section('title')
     내용 {{ $type == '' ? '입력' : '수정' }} | {{ Cache::get('config.homepage')->title }}
@endsection

@section('include_script')
    <script src="{{ asset('js/common.js') }}"></script>
    <script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
    <script>
        var menuVal = 300500
    </script>
@endsection

@section('content')
<div class="body-contents">
    <form name="contentform" onsubmit="return contentFormCheck(this);" @if($type == "update") action="{{ route('contents.update', $content->id)}}" @else action="{{ route('contents.store')}}" @endif method="post" enctype="multipart/form-data">
    <input type="hidden" name="type" value="{{ $type }}">
    <input type="hidden" name="html" value="1">
    @if($type == "update")
        {{ method_field('PUT')}}
    @else
        {{ method_field('POST')}}
    @endif
    {{ csrf_field() }}
    <table>
    <caption>내용 관리 목록</caption>
    <colgroup>
        <col class="grid_4">
        <col>
    </colgroup>
    <tbody>
    <tr>
        <th scope="row"><label for="co_id">ID</label></th>
        <td>
            <span class="frm_info">20자 이내의 영문자, 숫자, _ 만 가능합니다.</span><br />
            <input type="text" name="content_id" id ="content_id" required @if($type == "update") value="{{ $content->content_id }}" readonly @else value="{{ old('content_id') }}" @endif size="20" maxlength="20">
            @foreach ($errors->get('content_id') as $message)
                <span class="help-block">
                    <strong>{{ $message }}</strong>
                </span>
            @endforeach
            @if($type == "update")
                <a href="{{ route('contents.show', $content->content_id) }}" class="btn_frmline">내용확인</a>
            @endif
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="subject">제목</label></th>
        <td>
            <input type="text" name="subject" id="subject" @if($type == "update") value="{{ $content->subject }}" @else value="{{ old('subject') }}" @endif required size="90">
            @foreach ($errors->get('subject') as $message)
                <span class="help-block">
                    <strong>{{ $message }}</strong>
                </span>
            @endforeach
        </td>
    </tr>
    <tr>
        <th scope="row">내용</th>
        <td>
            <div style="border: 1px solid #ccc; background: #fff; min-height: 400px; border-radius: 4px; box-sizing: border-box;">
                <textarea name="content" id="content" class="editorArea">@if($type == "update"){{ $content->content }} @else {{ old('content') }} @endif</textarea>
            </div>
            @foreach ($errors->get('content') as $message)
                <span class="help-block">
                    <strong>{{ $message }}</strong>
                </span>
            @endforeach
        </td>
    </tr>
    <tr>
        <th scope="row">모바일 내용</th>
        <td>
            <div style="border: 1px solid #ccc; background: #fff; min-height: 400px; border-radius: 4px; box-sizing: border-box;">
                <textarea name="mobile_content" id="mobile_content" class="editorArea">@if($type == 'update'){{ $content->mobile_content }}@endif</textarea>
            </div>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="skin">스킨 디렉토리</label></th>
        <td>
            <select name="skin" id="skin">
            @foreach($skinList as $key => $value)
                <option value="{{ $key }}" @if($type == "update" && $key == $content->skin) selected @endif>
                    {{ $value }}
                </option>
            @endforeach
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="mobile_skin">모바일스킨 디렉토리</label></th>
        <td>
            <select name="mobile_skin" id="mobile_skin">
            @foreach($mobileSkinList as $key => $value)
                <option value="{{ $key }}" @if($type == "update" && $key == $content->mobile_skin) selected @endif>
                    {{ $value }}
                </option>
            @endforeach
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="tag_filter_use">태그 필터링 사용</label></th>
        <td>
            <span class="frm_info">내용에서 iframe 등의 태그를 사용하려면 사용안함으로 선택해 주십시오.</span><br />
            <select name="tag_filter_use" id="tag_filter_use">
                <option value="0" @if($type == 'update' && $content->tag_filter_use == 0) selected @endif>사용안함</option>
                <option value="1" @if($type == 'update' && $content->tag_filter_use == 1) selected @endif>사용함</option>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="include_head">상단 파일 경로</label></th>
        <td>
            <span class="frm_info">설정값이 없으면 기본 상단 파일을 사용합니다.</span><br />
            <input type="text" name="include_head" @if($type == 'update') value="{{ $content->include_head}}" @endif id="include_head" class="frm_input" size="60">
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="include_tail">하단 파일 경로</label></th>
        <td>
            <span class="frm_info">설정값이 없으면 기본 하단 파일을 사용합니다.</span><br />
            <input type="text" name="include_tail" @if($type == 'update') value="{{ $content->include_tail}}" @endif id="include_tail" class="frm_input" size="60">
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="himg">상단이미지</label></th>
        <td>
            <input type="file" name="himg" id="himg">
            @if($type == 'update' && $existHeadImage)
                <input type="checkbox" name="himg_del" value="1" id="himg_del"> <label for="himg_del">삭제</label>
                <div class="banner_or_img">
                    <img src="/storage/content/{{ $content->content_id }}_h" width="{{ $headImageWidth }}" alt="">
                </div>
            @endif
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="timg">하단이미지</label></th>
        <td>
            <input type="file" name="timg" id="timg">
            @if($type == 'update' && $existTailImage)
                <input type="checkbox" name="timg_del" value="1" id="timg_del"> <label for="timg_del">삭제</label>
                <div class="banner_or_img">
                    <img src="/storage/content/{{ $content->content_id }}_t" width="{{ $tailImageWidth }}" alt="">
                </div>
            @endif
        </td>
    </tr>
    </tbody>
    </table>
        <input type="submit" value="확인" class="btn_submit">
        <a href="{{ route('contents.index') }}">목록</a>
    </form>
</div>
<script>
function contentFormCheck(f)
{
    var errmsg = "";
    var errfld = null;

    if(tinymce.get('content').getContent().length == 0) {
        errmsg += "내용을 입력하세요." + "\n";
    }

    if (errmsg != "") {
        alert(errmsg);
        errfld.focus();
        return false;
    }

    return true;
}

tinymce.init({
    selector: '.editorArea',
    language: 'ko_KR',
    branding: false,
    theme: "modern",
    skin: "lightgray",
    height: 400,
    min_height: 400,
    min_width: 750,
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
</script>
@endsection
