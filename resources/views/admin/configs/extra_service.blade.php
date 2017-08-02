@extends('admin.admin')

@section('title')
    부가서비스 | {{ Cache::get("config.homepage")->title }}
@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>부가서비스</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">환경설정</li>
            <li class="depth">부가서비스</li>
        </ul>
    </div>
</div>
<div id="body_tab_type2">
    <span class="txt">아래의 서비스들은 라온보드에서 이미 지원하는 기능으로 별도의 개발이 필요 없으며, 서비스 신청 후 바로 사용할 수 있습니다.</span>
</div>
<div class="body-contents">
    <div class="sevice_1">
        <span class="circle_icon">
            <i class="fa fa-mobile" aria-hidden="true"></i>
        </span>
        <h3>휴대폰 본인확인 서비스</h3>
        <p class="sevice_info">
            정보통신망법 23조 2항(주민등록번호의 사용제한)에 따라 기존 주민등록번호 기반의 인증서비스 이용이 불가합니다. 주민등록번호 대체수단으로 최소한의 정보(생년월일, 휴대폰번호, 성별)를 입력받아 본인임을 확인하는 인증수단 입니다.
        </p>
        <ul>
            <li><a href="http://sir.kr/main/service/p_cert.php" target="_blank"><img src="{{ asset('images/svc_btn_01.jpg') }}" alt="KCP 휴대폰 본인확인 신청하기" title=""></a></li>
            <li><a href="http://sir.kr/main/service/lg_cert.php" target="_blank"><img src="{{ asset('images/svc_btn_02.jpg') }}" alt="LG유플러스 휴대폰대체인증 신청하기" title=""></a></li>
            <li class="last"><a href="http://sir.kr/main/service/b_cert.php" target="_blank"><img src="{{ asset('images/svc_btn_03.jpg') }}" alt="OKname 휴대폰 본인확인 신청하기" title=""></a></li>
        </ul>
    </div>
    <div class="sevice_1">
        <span class="circle_icon" style="background: #f0cd67;">
            <img src="{{ asset('images/ipin.gif') }}">
        </span>
        <h3>아이핀 본인확인서비스</h3>
        <p class="sevice_info">
            정부가 주관하는 주민등록번호 대체 수단으로 본인의 개인정보를 아이핀 사이트에 한번만 발급해 놓고, 이후부터는 아이디와 패스워드 만으로 본인임을 확인하는 인증수단 입니다.
        </p>
        <ul>
            <li class="item_1"><a href="http://sir.kr/main/service/b_ipin.php" target="_blank"><img src="{{ asset('images/svc_btn_03.jpg') }}" alt="OKname 아이핀 본인확인 신청하기" title=""></a></li>
        </ul>
    </div>
    <div class="sevice_1">
        <span class="circle_icon" style="background: #6686f2;">
            <i class="fa fa-envelope" aria-hidden="true"></i>
        </span>
        <h3>SMS 문자 서비스</h3>
        <p class="sevice_info">
            사이트 관리자 또는 회원이 다른 회원의 휴대폰으로 단문메세지(최대 한글 40자, 영문 80자)를 발송할 수 있습니다.
        </p>
        <ul>
            <li class="item_1"><a href="http://www.icodekorea.com/?s=res&ctl=user_sign_on&act=agree&sellid=sir2&s=res&type=A" target="_blank"><img src="{{ asset('images/svc_btn_05.jpg') }}" alt="OKname 아이핀 본인확인 신청하기" title=""></a></li>
        </ul>
    </div>

</div>
@endsection
<script>
    var menuVal = 100810
</script>
