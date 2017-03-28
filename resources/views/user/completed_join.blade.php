@extends('theme')

@section('title')
    LaBoard | 회원가입 완료
@endsection

@section('content')
<div class="container">
    {{ $user->nick }}님의 회원가입을 진심으로 축하합니다.<br />
    @if($emailCertify == 1)
    회원 가입 시 입력하신 이메일 주소로 인증메일이 발송되었습니다.<br />
    발송된 인증메일을 확인하신 후 인증처리를 하시면 사이트를 원활하게 이용하실 수 있습니다.<br />
    <p>
        이메일 주소 {{ $user->email }}
    </p>
    이메일 주소를 잘못 입력하셨다면, 사이트 관리자에게 문의해주시기 바랍니다.<br />
    @endif
    회원님의 비밀번호는 아무도 알 수 없는 암호화 코드로 저장되므로 안심하셔도 좋습니다.<br />
    아이디, 비밀번호 분실시에는 회원가입시 입력하신 이메일 주소를 이용하여 찾을 수 있습니다.<br />
    회원 탈퇴는 언제든지 가능하며 일정기간이 지난 후, 회원님의 정보는 삭제하고 있습니다.<br />
    감사합니다.<br />
    <a href="{{ route('home') }}">메인으로</a>
</div>
@endsection
