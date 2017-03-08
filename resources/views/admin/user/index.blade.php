@extends('layouts.app')

@section('content')
    <table class="table table-hover">
        <thead>
            <th>이메일</th>
            <th>이름</th>
            <th>닉네임</th>
            {{-- <th>메일인증</th> --}}
            <th>정보공개</th>
            <th>메일수신</th>
            <th>SMS수신</th>
            {{-- <th>성인인증</th> --}}
            {{-- <th>접근차단</th> --}}
            <th>휴대폰</th>
            <th>전화번호</th>
            <th>상태/권한</th>
            <th>포인트</th>
            <th>최종접속</th>
            <th>가입일</th>
            {{-- <th>접근그룹</th> --}}
            <th>관리</th>
        </thead>

        <tbody>
        @foreach ($users as $user)
            <tr>
                <td>{{ $user->email }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->nick }}</td>
                {{-- <td>{{ $user->email_certify }}</td> --}}
                <td>{{ $user->open }}</td>
                <td>{{ $user->mailing }}</td>
                <td>{{ $user->sms }}</td>
                {{-- <td>{{ $user->adult }}</td> --}}
                {{-- <td>{{ $user->intercept_date }}</td> --}}
                <td>{{ $user->hp }}</td>
                <td>{{ $user->tel }}</td>
                <td>{{ $user->level }}</td>
                <td>{{ $user->point }}</td>
                <td>{{ $user->today_login }}</td>
                <td>{{ $user->datetime }}</td>
                {{-- <td>{{ $user->nick }}</td> --}}
                <td><button>수정</button></td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
