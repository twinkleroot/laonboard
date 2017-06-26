@extends('admin.admin')

@section('title')
    부가서비스 | {{ Cache::get("config.homepage")->title }}
@endsection

@section('content')
    휴대폰 본인확인 서비스<br />
    <a href="https://sir.kr/main/service/p_cert.php">KCP</a>
    <a href="https://sir.kr/main/service/lg_cert.php">U+전자결제</a>
    <a href="https://sir.kr/main/service/b_cert.php">okname</a><br />
    아이핀 본인확인서비스<br />
    <a href="https://sir.kr/main/service/b_ipin.php">okname</a><br />
    SMS 문자 서비스<br />
    <a href="http://www.icodekorea.com/?s=res&ctl=user_sign_on&act=agree&sellid=sir2&s=res&type=A">iCODE</a>
@endsection
<script>
    var menuVal = 101100
</script>
