@extends('layouts.app')

@section('title')
    LaBoard | 회원 관리
@endsection

@section('content')
@if(Session::has('message'))
  <div class="alert alert-info">
    {{Session::get('message') }}
  </div>
@endif
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">회원 관리</div>
            <div class="panel-heading"><a class="btn btn-default" href={{ route('users.create')}}>회원 추가</a></div>
            <div class="panel-body">
                <table class="table table-hover">
                    <thead>
                        <th class="text-center"><input type="checkbox" name="checkAll" id="checkAll" /></th>
                        <th class="text-center">이메일</th>
                        <th class="text-center">이름</th>
                        <th class="text-center">닉네임</th>
                        {{-- <th>메일인증</th> --}}
                        <th class="text-center">정보공개</th>
                        <th class="text-center">메일수신</th>
                        <th class="text-center">SMS수신</th>
                        {{-- <th>성인인증</th> --}}
                        {{-- <th>접근차단</th> --}}
                        <th class="text-center">휴대폰</th>
                        <th class="text-center">전화번호</th>
                        <th class="text-center">상태/권한</th>
                        <th class="text-center">포인트</th>
                        <th class="text-center">최종접속</th>
                        <th class="text-center">가입일</th>
                        {{-- <th>접근그룹</th> --}}
                        <th class="text-center">관리</th>
                    </thead>

                    <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td><input type="checkbox" name="id" id="userId" value={{ $user->id }}/></td>
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
                            <td><a class="btn btn-primary" href="{{ route('users.edit', $user->id) }}">수정</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="panel-heading"><a class="btn btn-default" href={{ route('users.destroy')}}>선택 삭제</a></div>
        </div>
    </div>
</div>
@endsection
