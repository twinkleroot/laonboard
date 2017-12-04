@extends('admin.layouts.basic')

@section('title')팝업 레이어 {{ $type == 'create' ? '입력' : '수정' }} | {{ cache('config.homepage')->title }}@endsection

@section('include_css')
<link rel="stylesheet" type="text/css" href="{{ ver_asset('bootstrap-colorpicker/css/bootstrap-colorpicker.min.css') }}">
@endsection

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
<script src="{{ ver_asset('bootstrap-colorpicker/js/bootstrap-colorpicker.js') }}"></script>
<script>
    var menuVal = 400100;
</script>
@include("common.tinymce")
@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>팝업 레이어 {{ $type == 'create' ? '입력' : '수정' }}</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">모듈 관리</li>
            <li class="depth">설치된 모듈</li>
            <li class="depth">팝업 레이어 {{ $type == 'create' ? '입력' : '수정' }}</li>
        </ul>
    </div>
</div>
<form id="popupForm" class="form-horizontal" role="form" method="POST" action="{{ $type =='create' ? route('admin.popup.store') : route('admin.popup.update', $popup->id) }}" onsubmit="return popupFormCheck(this);" >
    {{ csrf_field() }}
    <input type="hidden" name="device" value="both">
<div id="body_tab_type2">
    <span class="txt">초기화면 접속 시 자동으로 뜰 팝업레이어를 설정합니다.</span>
    <div class="submit_btn">
        <button type="submit" class="btn btn-sir">확인</button>
        <a class="btn btn-default" href="{{ route('admin.popup.index') }}">목록</a>
    </div>
</div>
<div class="body-contents">
    @if ($errors->any())
    <div id="adm_save">
        <span class="adm_save_txt">{{ $errors->first() }}</span>
        <button onclick="alertclose()" class="adm_alert_close">
            <i class="fa fa-times"></i>
        </button>
    </div>
    @endif
    <div id="admin_box1" class="adm_box">
        <div class="adm_panel">
            <div class="adm_box_bd">
                @if($type == 'create')
                    {{ method_field('post') }}
                    <input type="hidden" name="content_html" value="{{ $default['content_html'] }}" />
                @else
                    {{ method_field('put') }}
                @endif
                {{-- <div class="form-group">
                    <label for="nw_device" class="col-md-2 control-label">접속기기</label>
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-4">
                                <select name="device" id="nw_device" class="form-control">
                                    <option value="both" @if($type == 'create' || ($type == 'update' && $popup->device == 'both')) selected @endif>PC와 모바일</option>
                                    <option value="pc" @if($type == 'update' && $popup->device == 'pc') selected @endif>PC</option>
                                    <option value="mobile" @if($type == 'update' && $popup->device == 'mobile') selected @endif>모바일</option>
                                </select>
                            </div>
                        </div>
                        <p class="help-block">팝업레이어가 표시될 접속기기를 설정합니다.</p>
                    </div>
                </div> --}}
                <div class="form-group">
                    <label for="disable_hours" class="col-md-2 control-label">시간</label>
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-1">
                                <input type="text" class="form-control required" name="disable_hours" id="disable_hours" required value="{{ $type == 'update' ? $popup->disable_hours : $default['disable_hours'] }}" size="5">
                            </div>
                            시간
                        </div>
                        <p class="help-block">고객이 다시 보지 않음을 선택할 시 몇 시간동안 팝업레이어를 보여주지 않을지 설정합니다.</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="begin_time" class="col-md-2 control-label">시작일시</label>
                    <div class="col-md-3">
                        <input type="text" class="form-control required" name="begin_time" id="begin_time" value="{{ $type == 'update' ? $popup->begin_time : '' }}" required>
                    </div>
                    <div class="col-md-3">
                        <input type="checkbox" name="begin_chk" id="begin_chk" value="{{ Carbon\Carbon::today() }}" onclick="if (this.checked == true) this.form.begin_time.value=this.form.begin_chk.value; else this.form.begin_time.value = this.form.begin_time.defaultValue;">
                        <label for="begin_chk">시작일시를 오늘로</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="end_time" class="col-md-2 control-label">종료일시</label>
                    <div class="col-md-3">
                        <input type="text" class="form-control required" name="end_time" id="end_time" value="{{ $type == 'update' ? $popup->end_time : '' }}" required>
                    </div>
                    <div class="col-md-3">
                        <input type="checkbox" name="end_chk" id="end_chk" value="{{ Carbon\Carbon::now()->addDays(7)->setTime(23, 59, 59) }}" onclick="if (this.checked == true) this.form.end_time.value=this.form.end_chk.value; else this.form.end_time.value = this.form.end_time.defaultValue;">
                        <label for="end_chk">종료일시를 오늘로부터 7일 후로</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="left" class="col-md-2 control-label">팝업레이어 좌측 위치</label>
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-1">
                                <input type="text" class="form-control required" name="left" id="left" value="{{ $type == 'update' ? $popup->left : $default['left'] }}" required size="5">
                            </div>
                            px
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="top" class="col-md-2 control-label">팝업레이어 상단 위치</label>
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-1">
                                <input type="text" class="form-control required" name="top" id="top" value="{{ $type == 'update' ? $popup->top : $default['top'] }}" required size="5">
                            </div>
                            px
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="width" class="col-md-2 control-label">팝업레이어 넓이</label>
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-1">
                                <input type="text" class="form-control required" name="width" id="width" value="{{ $type == 'update' ? $popup->width : $default['width'] }}" required size="5">
                            </div>
                            px
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="height" class="col-md-2 control-label">팝업레이어 높이</label>
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-1">
                                <input type="text" class="form-control required" name="height" id="height" value="{{ $type == 'update' ? $popup->height : $default['height'] }}" required size="5">
                            </div>
                            px
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="color" class="col-md-2 control-label">팝업레이어 색상</label>
                    <div class="col-md-2">
                        <div id="cp1" class="input-group colorpicker-component">
                            <input type="text" name="color" id="color" class="form-control" value="{{ $type == 'update' ? $popup->color : $default['color'] }}" />
                            <span class="input-group-addon"><i></i></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="color_button" class="col-md-2 control-label">팝업레이어 버튼 색상</label>
                    <div class="col-md-2">
                        <div id="cp2" class="input-group colorpicker-component">
                            <input type="text" name="color_button" id="color_button" class="form-control" value="{{ $type == 'update' ? $popup->color_button : $default['color_button'] }}" />
                            <span class="input-group-addon"><i></i></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="color_button_font" class="col-md-2 control-label">팝업레이어 버튼 폰트 색상</label>
                    <div class="col-md-2">
                        <div id="cp3" class="input-group colorpicker-component">
                            <input type="text" name="color_button_font" id="color_button_font" class="form-control" value="{{ $type == 'update' ? $popup->color_button_font : $default['color_button'] }}" />
                            <span class="input-group-addon"><i></i></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="subject" class="col-md-2 control-label">팝업 제목</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control required" name="subject" id="subject" value="{{ $type == 'update' ? $popup->subject : '' }}" required size="80">
                    </div>
                </div>
                <div class="form-group">
                    <label for="content" class="col-md-2 control-label">내용</label>
                    <div class="col-md-7">
                        <textarea class="form-control editorArea" name="content" id="content" rows="10">{{ $type == 'update' ? $popup->content : '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
<script>
$(function() {
    $('#cp1').colorpicker();
    $('#cp2').colorpicker();
    $('#cp3').colorpicker();
});
function popupFormCheck(f)
{
    var errmsg = "";
    var errfld = null;

    if(tinymce.get('content').getContent().length == 0) {
        errmsg += "내용을 입력하세요." + "\n";
    }

    if(f.subject.value == '') {
        errmsg += '제목을 입력하세요.';
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
