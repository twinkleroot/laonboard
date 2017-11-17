@extends('admin.layouts.basic')

@section('title')본인 확인 설정 | {{ cache('config.homepage')->title }}@endsection

@section('include_script')
<script type="text/javascript">
    var menuVal = 400100;

    function formSubmit() {
        $("#certifyForm").submit();
    };
</script>
@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>본인 확인 설정</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">모듈 관리</li>
            <li class="depth">설치된 모듈</li>
            <li class="depth">본인 확인 설정</li>
        </ul>
    </div>
</div>

<div id="body_tab_type2">
    <span class="txt">회원가입/회원정보수정과 회원관리/게시판관리에 휴대폰 본인인증을 추가하는 모듈입니다.</span>
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

<form role="form" method="POST" id="certifyForm" action="{{ route('admin.certify.update') }}">
    {{ method_field('PUT') }}
    {{ csrf_field() }}
    <section id="cfs_cert" class="adm_box">
        <div class="adm_box_hd">
            <span class="adm_box_title">본인확인 설정</span>
        </div>
        <table class="adm_box_table">
            <tr>
                <td class="table_body" colspan="2">
                    회원가입 시 본인확인 수단을 설정합니다.<br>
                    실명과 휴대폰 번호 그리고 본인확인 당시에 성인인지의 여부를 저장합니다.<br>
                    게시판의 경우 본인확인 또는 성인여부를 따져 게시물 조회 및 쓰기 권한을 줄 수 있습니다.<br>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="certUse">본인확인</label>
                </th>
                <td class="table_body">
                    <select name="certUse" class="form-control form_small">
                        <option value='0' @if($configCert->certUse == 0) selected @endif>사용안함</option>
                        <option value='1' @if($configCert->certUse == 1) selected @endif>테스트</option>
                        <option value='2' @if($configCert->certUse == 2) selected @endif>실서비스</option>
                    </select>
                </td>
            </tr>
            {{-- <tr>
                <th>
                    <label for="certIpin">아이핀 본인확인</label>
                </th>
                <td class="table_body">
                    <select name='certIpin' class="form-control form_large">
                        <option value @if(!$configCert->certIpin) selected @endif>사용안함</option>
                        <option value='kcb' @if($configCert->certIpin == 'kcb') selected @endif>코리아크레딧뷰로(KCB) 아이핀</option>
                    </select>
                </td>
            </tr> --}}
            <tr>
                <th>
                    <label for="certHp">휴대폰 본인확인</label>
                </th>
                <td class="table_body">
                    <select name='certHp' class="form-control form_large">
                        <option value @unless($configCert->certHp) selected @endunless>사용안함</option>
                        <option value='kcb' @if($configCert->certHp == 'kcb') selected @endif>코리아크레딧뷰로(KCB) 휴대폰 본인확인</option>
                        {{-- <option value='kcp' @if($configCert->certHp == 'kcp') selected @endif>NHN KCP 휴대폰 본인확인</option>
                        <option value='lg' @if($configCert->certHp == 'lg') selected @endif>LG유플러스 휴대폰 본인확인</option> --}}
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="certKcbCd">코리아크레딧뷰로 KCB 회원사ID</label>
                </th>
                <td class="table_body">
                    <input type="text" name="certKcbCd" class="form-control form_middle" value="{{ $configCert->certKcbCd }}">
                    <span class="help-block">KCB 회원사ID를 입력해 주십시오.<br />
                    서비스에 가입되어 있지 않다면, KCB와 계약체결 후 회원사ID를 발급 받으실 수 있습니다.<br />
                    이용하시려는 서비스에 대한 계약을 아이핀, 휴대폰 본인확인 각각 체결해주셔야 합니다.<br />
                    아이핀 본인확인 테스트의 경우에는 KCB 회원사ID가 필요 없으나,<br />
                    휴대폰 본인확인 테스트의 경우 KCB 에서 따로 발급 받으셔야 합니다.</span>
                    <div style="margin-top: 8px;">
                        {{-- <a href="http://sir.kr/main/service/b_ipin.php" class="btn btn-sir" target="_blank">KCB 아이핀 서비스 신청페이지</a> --}}
                        <a href="http://sir.kr/main/service/b_cert.php" class="btn btn-sir" target="_blank">KCB 휴대폰 본인확인 서비스 신청페이지</a>
                    </div>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="certLimit">본인확인 이용제한</label>
                </th>
                <td class="table_body">
                    <input type="text" name="certLimit" class="form-control form_num" value="{{ $configCert->certLimit }}">회

                    <span class="help-block">
                        하루동안 아이핀과 휴대폰 본인확인 인증 이용회수를 제한할 수 있습니다.<br />
                        회수제한은 실서비스에서 아이핀과 휴대폰 본인확인 인증에 개별 적용됩니다.<br />
                        0 으로 설정하시면 회수제한이 적용되지 않습니다.
                    </span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="certReq">본인확인 필수</label>
                </th>
                <td class="table_body">
                    <input type="checkbox" name="certReq" id="certReq" value="1" @if($configCert->certReq == 1) checked @endif>
                                <label for="certReq">예</label>

                    <span class="help-block">
                        회원가입 때 본인확인을 필수로 할지 설정합니다. 필수로 설정하시면 본인확인을 하지 않은 경우 회원가입이 안됩니다.
                    </span>
                </td>
            </tr>
        </table>
    </section>
</form>
</div>
@stop
