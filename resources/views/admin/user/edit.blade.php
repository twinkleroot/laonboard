@extends('layouts.app')

@section('title')
    LaBoard | 회원 수정
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">회원 수정</div>
                <form class="form-horizontal" role="form" method="POST" action="{{ route('users.update', $id) }}">
                    {{ csrf_field() }}
                    <table class="table table-hover">
                        <tr>
                            <th>이메일</th>
                            <td><input type="text" class="form-control" value="{{ $user->email }}" readonly/></td>
                            <th>비밀번호</th>
                            <td><input type="text" class="form-control" value="" /></td>
                        </tr>
                        <tr>
                            <th>이름</th>
                            <td><input type="text" class="form-control" name="name" value="{{ $user->name }}" /></td>
                            <th>닉네임</th>
                            <td><input type="text" class="form-control" name="nick" value="{{ $user->nick }}" /></td>
                        </tr>
                        <tr>
                            <th>회원 권한</th>
                            <td><input type="text" class="form-control" name="level" value="{{ $user->level }}" /></td>
                            <th>포인트</th>
                            <td><input type="text" class="form-control" name="point" value="{{ $user->point }}" /></td>
                        </tr>
                        <tr>
                            <th>홈페이지</th>
                            <td><input type="text" class="form-control" name="homepage" value="{{ $user->homepage }}" /></td>
                        </tr>
                        <tr>
                            <th>휴대폰번호</th>
                            <td><input type="text" class="form-control" name="hp" value="{{ $user->hp }}" /></td>
                            <th>전화번호</th>
                            <td><input type="text" class="form-control" name="tel" value="{{ $user->tel }}" /></td>
                        </tr>
                        <tr>
                            <th>본인확인방법</th>
                            <td><input type="radio" name="certify_case" value="" />아이핀
                                <input type="radio" name="certify_case" value="" />휴대폰</td>
                        </tr>
                        <tr>
                            <th>본인확인</th>
                            <td>
                                <input type="radio" name="certify" @if($user->certify === 1) checked @endif value="1" />예
                                <input type="radio" name="certify" @if($user->certify === 0 || empty($user->certify)) checked @endif value="0" />아니오
                            </td>
                            <th>성인인증</th>
                            <td>
                                <input type="radio" name="adult" @if($user->adult === 1) checked @endif value="1" />예
                                <input type="radio" name="adult" @if($user->adult === 0) checked @endif value="0" />아니오
                            </td>
                        </tr>
                        <tr>
                            <th>주소</th>
                            <td></td>
                        </tr>
                        <tr>
                            <th>회원아이콘</th>
                            <td>이미지 크기는 넓이 22픽셀 높이 22픽셀로 해주세요.<br />
                                <input type="file" name="icon" value="" />
                            </td>
                        </tr>
                        <tr>
                            <th>메일 수신</th>
                            <td>
                                <input type="radio" name="mailing" @if($user->mailing === 1) checked @endif value="1" />예
                                <input type="radio" name="mailing" @if($user->mailing === 0) checked @endif value="0" />아니오
                            </td>
                            <th>SMS 수신</th>
                            <td>
                                <input type="radio" name="sms" @if($user->sms === 1) checked @endif value="1" />예
                                <input type="radio" name="sms" @if($user->sms === 0) checked @endif value="0" />아니오
                            </td>
                        </tr>
                        <tr>
                            <th>정보 공개</th>
                            <td>
                                <input type="radio" name="open" @if($user->open === 1) checked @endif value="1" />예
                                <input type="radio" name="open" @if($user->open === 0) checked @endif value="0" />아니오
                            </td>
                        </tr>
                        <tr>
                            <th>서명</th>
                            <td>
                                <textarea name="signature" class="form-control"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>자기 소개</th>
                            <td>
                                <textarea name="profile" class="form-control"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>메모</th>
                            <td>
                                <textarea name="memo" class="form-control"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>회원가입일</th>
                            <td>
                                {{ $user->datetime }}
                            </td>
                            <th>최근접속일</th>
                            <td>
                                {{ $user->today_login }}
                            </td>
                        </tr>
                        <tr>
                            <th>IP</th>
                            <td>
                                {{ $user->ip }}
                            </td>
                        </tr>
                        <tr>
                            <th>탈퇴일자</th>
                            <td>
                                <input type="text" class="form-control" name="leave_date" value="{{ $user->leave_date }}" />
                                <input type="checkbox" name="leave_date" value="" />탈퇴일을 오늘로 지정
                            </td>
                            <th>접근차단일자</th>
                            <td>
                                <input type="text" class="form-control" name="intercept_date" value="{{ $user->intercept_date }}" />
                                <input type="checkbox" name="intercept_date" value="" />접근차단일을 오늘로 지정
                            </td>
                        </tr>
                    </table>
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                {{ method_field('PUT') }}
                                확인
                            </button>
                            <a class="btn btn-primary" href="{{ route('users.index') }}">목록</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
