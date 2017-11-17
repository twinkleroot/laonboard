@extends('admin.layouts.basic')

@section('title')메인 페이지 관리 | {{ Cache::get('config.homepage')->title }}@stop

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
<script>
    var menuVal = 400100;
</script>
@stop

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>메인 페이지 관리</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">모듈 관리</li>
            <li class="depth">설치된 모듈</li>
            <li class="depth">메인 페이지 관리</li>
        </ul>
    </div>
</div>
<form name="contentform" action="{{ route('admin.custommain.update') }}" method="POST">
    {{ csrf_field() }}
    {{ method_field('put') }}
<div id="body_tab_type2">
    <span class="txt">메인 페이지를 구성합니다. 우선순위에 따라 메인에 표시됩니다.</span>
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

    <table class="table table-striped box">
        <thead>
            <tr>
                <td>이벤트</td>
                <td>사용여부</td>
                <td>우선순위</td>
            </tr>
        </thead>
        <tbody>
            @foreach($events as $key => $value)
            <tr>
                <td class="td_subject">
                    <label for="skin">{{ $value['description'] }}</label>
                </td>
                <td class="td_mngsmall">
                    <select name="{{ $key."-use" }}" id="{{ $key."-use" }}" class="form-control form_middle">
                        <option value="1" @if($value['use'] == 1) selected @endif>사용함</option>
                        <option value="0" @if($value['use'] == 0) selected @endif>사용안함</option>
                    </select>
                </td>
                <td class="td_mngsmall">
                    <select name="{{ $key."-priority" }}" id="{{ $key."-priority" }}" class="form-control form_middle">
                        @for($i=1; $i<=10; $i++)
                        <option value="{{ $i }}" @if($value['priority'] == $i) selected @endif>{{ $i }}</option>
                        @endfor
                    </select>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</form>
@stop
