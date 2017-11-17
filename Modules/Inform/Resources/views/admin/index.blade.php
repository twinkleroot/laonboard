@extends('admin.layouts.basic')

@section('title')알림 설정 | {{ Cache::get('config.homepage')->title }}@stop

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
<script>
    var menuVal = 400100;
</script>
@stop

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>알림 설정</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">모듈 관리</li>
            <li class="depth">설치된 모듈</li>
            <li class="depth">알림 설정</li>
        </ul>
    </div>
</div>
<form name="informForm" action="{{ route('admin.inform.update') }}" method="POST">
    {{ csrf_field() }}
    {{ method_field('put') }}
<div id="body_tab_type2">
    <span class="txt">글,댓글쓰기 알림 설정입니다.</span>
    <div class="submit_btn">
        <button type="submit" class="btn btn-sir">설정변경</button>
        <a class="btn btn-default" href="{{ route('admin.modules.index') }}">모듈목록</a>
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
        <span class="adm_box_title">알림 설정</span>
    </div>
    <section class="adm_box first">
        <table class="adm_box_table">
            <tr>
                <th>알림 삭제</th>
                <td class="table_body">
                    <input type="text" name="del" class="form-control form_num" value="{{ cache('config.inform')->del }}">
                    <span class="help-block">설정일이 지난 알림 자동 삭제</span>
                </td>
            </tr>
        </table>
    </section>
</div>
</form>
@stop
