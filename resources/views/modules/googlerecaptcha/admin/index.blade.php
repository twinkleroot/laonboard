@extends('admin.layouts.basic')

@section('title')구글 리캡챠 설정 | {{ Cache::get('config.homepage')->title }}@stop

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
<script>
    var menuVal = 400100;

    function formSubmit() {
        $("#recaptchaForm").submit();
    };
</script>
@stop

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>구글 리캡챠(Google Invisible reCAPTCHA)</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">모듈 관리</li>
            <li class="depth">설치된 모듈</li>
            <li class="depth">구글 리캡챠 설정</li>
        </ul>
    </div>
</div>
    <div id="body_tab_type2">
        <span class="txt">자동 등록 방지 모듈로 구글 리캡챠(Google Invisible reCAPTCHA)를 쓰고자 할 때 필요한 설정입니다.</span>
        <div class="submit_btn">
            <button type="button" class="btn btn-sir" onclick="formSubmit();">설정변경</button>
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

    <form role="form" method="POST" name="recaptchaForm" id="recaptchaForm" action="{{ route('admin.googlerecaptcha.update') }}">
        {{ method_field('PUT') }}
        {{ csrf_field() }}
        <section id="cfs_cert" class="adm_box">
            <div class="adm_box_hd">
                <span class="adm_box_title">구글 리캡챠 설정</span>
            </div>
            <table class="adm_box_table">
                <tr>
                    <td class="table_body" colspan="2">
                        키가 없을 시 '키 얻기' 버튼을 클릭해서 구글에서 키를 얻어와야 합니다.
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="googleInvisibleClient">클라이언트 키</label>
                    </th>
                    <td class="table_body">
                        <input type="text" class="form-control form_large" name="googleInvisibleClient" value="{{ cache("config.recaptcha")->googleInvisibleClient }}" style="display: inline-block;">
                    <a href="https://www.google.com/recaptcha/admin" class="btn btn-sir ml15" target="_blank">키 얻기</a>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="googleInvisibleServer">서버 키</label>
                    </th>
                    <td class="table_body">
                        <input type="text" class="form-control form_large" name="googleInvisibleServer" value="{{ cache("config.recaptcha")->googleInvisibleServer }}" style="display: inline-block;">
                    <a href="https://www.google.com/recaptcha/admin" class="btn btn-sir ml15" target="_blank">키 얻기</a>
                    </td>
                </tr>
            </table>
        </section>
    </form>
    </div>
    @stop
