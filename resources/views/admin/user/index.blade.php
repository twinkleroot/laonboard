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
            <div class="panel-heading"><a class="btn btn-primary" href={{ route('users.create')}}>회원 추가</a></div>
            <?php $ids = ''; ?>
            <form class="form-horizontal" role="form" method="POST" action="{{ route('users.destroy', $ids) }}">
            <div class="panel-body">
                {{ csrf_field() }}
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
                            <td class="text-center"><input type="checkbox" name="id" id="userId" value={{ $user->id }}/></td>
                            <td class="text-center">{{ $user->email }}</td>
                            <td class="text-center">{{ $user->name }}</td>
                            <td class="text-center">{{ $user->nick }}</td>
                            {{-- <td class="text-center">{{ $user->email_certify }}</td> --}}
                            <td class="text-center"><input type='checkbox' value='1' {{ ($user->open == '1' ? 'checked' : '') }}/></td>
                            <td class="text-center"><input type='checkbox' value='1' {{ ($user->mailing == '1' ? 'checked' : '') }}/></td>
                            <td class="text-center"><input type='checkbox' value='1' {{ ($user->sms == '1' ? 'checked' : '') }}/></td>
                            {{-- <td class="text-center">{{ $user->adult }}</td> --}}
                            {{-- <td class="text-center">{{ $user->intercept_date }}</td> --}}
                            <td class="text-center">{{ $user->hp }}</td>
                            <td class="text-center">{{ $user->tel }}</td>
                            <td class="text-center">
                                <select>
                                    @for ($i=1; $i <= 10; $i++)
                                        <option value={{ $i }} {{ $user->level == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </td>
                            <td class="text-center">{{ $user->point }}</td>
                            <td class="text-center">{{ $user->today_login }}</td>
                            <td class="text-center">{{ $user->datetime }}</td>
                            {{-- <td class="text-center">{{ $user->nick }}</td> --}}
                            <td class="text-center"><a class="btn btn-primary" href="{{ route('users.edit', $user->id) }}">수정</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="panel-heading">
                <input type="submit" class="btn btn-primary" value="선택 수정"/>
                <input type="submit" class="btn btn-primary" value="선택 삭제"/>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection
