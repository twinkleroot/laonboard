@extends('admin.layouts.basic')

@section('title')내용 {{ $type == '' ? '입력' : '수정' }} | {{ cache('config.homepage')->title }}@endsection

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
<script>
    var menuVal = 400100;
</script>
@include("common.tinymce")
@endsection

@section('content')
<form name="contentform" onsubmit="return contentFormCheck(this);" action="{{ $type == "update" ? route('admin.content.update', $content->id) : route('admin.content.store') }}" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="type" value="{{ $type }}">
    <input type="hidden" name="html" value="1">
    {{ csrf_field() }}
@if($type == "update")
    {{ method_field('PUT')}}
@else
    {{ method_field('POST')}}
@endif
<div class="body-head">
    <div class="pull-left">
        <h3>내용 관리 추가</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">모듈 관리</li>
            <li class="depth">내용 관리 추가</li>
        </ul>
    </div>
</div>
<div id="body_tab_type2">
    <span class="txt">내용관리를 추가합니다.</span>
    <div class="submit_btn">
        <button type="submit" class="btn btn-sir">확인</button>
        <a class="btn btn-default" href="{{ route('admin.content.index') }}">목록</a>
    </div>
</div>
<div class="body-contents">
@if(Session::has('message'))
    <div id="adm_save">
        <span class="adm_save_txt">{{ Session::get('message') }}</span>
        <button onclick="alertclose()" class="adm_alert_close">
            <i class="fa fa-times"></i>
        </button>
    </div>
@endif
@if ($errors->any())
    <div id="adm_save">
        <span class="adm_save_txt">{{ $errors->first() }}</span>
        <button onclick="alertclose()" class="adm_alert_close">
            <i class="fa fa-times"></i>
        </button>
    </div>
@endif
    <div class="adm_box_hd">
        <span class="adm_box_title">내용관리 추가</span>
    </div>
    <table class="adm_box_table">
        <tr>
            <th>
                <label for="co_id">ID</label>
            </th>
            <td class="table_body">
                <input type="text" name="content_id" id ="content_id" required class="form-control form_middle required" @if($type == "update") value="{{ $content->content_id }}" readonly @else value="{{ old('content_id') }}" @endif size="20" maxlength="20">
                @if($type == "update")
                    <a href="{{ route('content.show', $content->content_id) }}" class="btn btn-sir" target="_blank">내용확인</a>
                @endif
                <span class="help-block">20자 이내의 영문자, 숫자, _ 만 가능합니다.</span>
            </td>
        </tr>
        <tr>
            <th>
                <label for="subject">제목</label>
            </th>
            <td class="table_body">
                <input type="text" name="subject" id="subject" class="form-control form_half required" @if($type == "update") value="{{ $content->subject }}" @else value="{{ old('subject') }}" @endif required size="90">
            </td>
        </tr>
        <tr>
            <th>
                <label for="content">내용</label>
            </th>
            <td class="table_body">
                <div style="background: #fff; min-height: 400px; border-radius: 4px; box-sizing: border-box;">
                    <textarea name="content" id="content" class="editorArea">@if($type == "update"){{ $content->content }} @else {{ old('content') }} @endif</textarea>
                </div>
            </td>
        </tr>
        {{-- <tr>
            <th>
                <label for="mobile_content">모바일 내용</label>
            </th>
            <td class="table_body">
                <div style="background: #fff; min-height: 400px; border-radius: 4px; box-sizing: border-box;">
                <textarea name="mobile_content" id="mobile_content" class="editorArea">@if($type == 'update'){{ $content->mobile_content }}@endif</textarea>
            </div>
            </td>
        </tr> --}}
        <tr>
            <th>
                <label for="skin">스킨</label>
            </th>
            <td class="table_body">
                <select name="skin" id="skin" class="form-control form_large">
                @foreach($skinList as $key => $value)
                    <option value="{{ $key }}" @if($type == "update" && $key == $content->skin) selected @endif>
                        {{ $value }}
                    </option>
                @endforeach
                </select>
            </td>
        </tr>
        {{-- <tr>
            <th>
                <label for="mobile_skin">모바일스킨 디렉토리</label>
            </th>
            <td class="table_body">
                <select name="mobile_skin" id="mobile_skin" class="form-control form_large">
                @foreach($mobileSkinList as $key => $value)
                    <option value="{{ $key }}" @if($type == "update" && $key == $content->mobile_skin) selected @endif>
                        {{ $value }}
                    </option>
                @endforeach
                </select>
            </td>
        </tr> --}}
        <tr>
            <th>
                <label for="tag_filter_use">태그 필터링 사용</label>
            </th>
            <td class="table_body">
                <select name="tag_filter_use" id="tag_filter_use" class="form-control form_middle">
                    <option value="0" @if($type == 'update' && $content->tag_filter_use == 0) selected @endif>사용안함</option>
                    <option value="1" @if($type == 'update' && $content->tag_filter_use == 1) selected @endif>사용함</option>
                </select>
                <span class="help-block">내용에서 iframe 등의 태그를 사용하려면 사용안함으로 선택해 주십시오.</span>
            </td>
        </tr>
        <tr>
            <th>
                <label for="include_head">하단 노출</label>
            </th>
            <td class="table_body">
                <select name="show" id="show" class="form-control form_middle">
                    <option value="0" @if($type == 'update' && $content->show == 0) selected @endif>숨김</option>
                    <option value="1" @if($type == 'update' && $content->show == 1) selected @endif>노출</option>
                </select>
                <span class="help-block">프론트 레이아웃 하단에 노출할 것인지 설정합니다.</span>
            </td>
        </tr>
        {{-- <tr>
            <th>
                <label for="include_head">상단 파일 경로</label>
            </th>
            <td class="table_body">
                <input type="text" name="include_head" class="form-control form_large" @if($type == 'update') value="{{ $content->include_head}}" @endif id="include_head" class="frm_input" size="60">
                <span class="help-block">설정값이 없으면 기본 상단 파일을 사용합니다.</span>
            </td>
        </tr>
        <tr>
            <th>
                <label for="include_tail">하단 파일 경로</label>
            </th>
            <td class="table_body">
                <input type="text" name="include_tail" class="form-control form_large" @if($type == 'update') value="{{ $content->include_tail}}" @endif id="include_tail" class="frm_input" size="60">
                <span class="help-block">설정값이 없으면 기본 하단 파일을 사용합니다.</span>
            </td>
        </tr> --}}
        <tr>
            <th>
                <label for="himg">상단이미지</label>
            </th>
            <td class="table_body">
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
            <th>
                <label for="timg">하단이미지</label>
            </th>
            <td class="table_body">
                <input type="file" name="timg" id="timg">
                @if($type == 'update' && $existTailImage)
                    <input type="checkbox" name="timg_del" value="1" id="timg_del"> <label for="timg_del">삭제</label>
                    <div class="banner_or_img">
                        <img src="/storage/content/{{ $content->content_id }}_t" width="{{ $tailImageWidth }}" alt="">
                    </div>
                @endif
            </td>
        </tr>
    </table>
</div>
</form>
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
</script>
@endsection
