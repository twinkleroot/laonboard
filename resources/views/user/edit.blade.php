@extends('layouts.app')

@section('title')
    LaBoard | 회원 정보 수정
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">회원 정보 수정</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('user.update') }}">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}
                        <div class="panel-heading">사이트 이용정보 입력</div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">이메일</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ $user->email }}" readonly>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">비밀번호</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">비밀번호 확인</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="panel-heading">개인정보 입력</div>

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">이름</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ $user->name }}" readonly>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('nick') ? ' has-error' : '' }}">
                            <label for="nick" class="col-md-4 control-label">닉네임</label>

                            <div class="col-md-6">
                                <p>
                                    공백없이 한글, 영문, 숫자만 입력 가능 <br />
                                    (한글2자, 영문4자 이상)<br />
                                    닉 네임을 바꾸시면 {{ config('gnu.nickDate') }}일 이내에는 변경할 수 없습니다.
                                </p>
                                <input id="nick" type="text" class="form-control" name="nick" value="{{ $user->nick }}" required autofocus>

                                @if ($errors->has('nick'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nick') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        @if(config('gnu.homepage') == 1)
                        <div class="form-group{{ $errors->has('homepage') ? ' has-error' : '' }}">
                            <label for="homepage" class="col-md-4 control-label">홈페이지</label>

                            <div class="col-md-6">
                                <input id="homepage" type="text" class="form-control" name="homepage" value="{{ $user->homepage }}">

                                @if ($errors->has('homepage'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('homepage') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if(config('gnu.tel') == 1)
                        <div class="form-group{{ $errors->has('tel') ? ' has-error' : '' }}">
                            <label for="tel" class="col-md-4 control-label">전화번호</label>

                            <div class="col-md-6">
                                <input id="tel" type="text" class="form-control" name="tel" value="{{ $user->tel }}">

                                @if ($errors->has('tel'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('tel') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if(config('gnu.hp') == 1)
                        <div class="form-group{{ $errors->has('hp') ? ' has-error' : '' }}">
                            <label for="hp" class="col-md-4 control-label">휴대폰번호</label>

                            <div class="col-md-6">
                                <input id="hp" type="text" class="form-control" name="hp" value="{{ $user->hp }}">

                                @if ($errors->has('hp'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('hp') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if(config('gnu.addr') == 1)
                        <div class="form-group{{ $errors->has('addr1') ? ' has-error' : '' }}">
                            <label for="addr1" class="col-md-4 control-label">주소</label>
                            <div class="col-md-6">
                                <input id="reg_zip" type="text" class="" name="zip" value="{{ $user->zip }}">
                                    <button>주소 검색</button>
                                <input id="addr1" type="text" class="" name="addr1" value="{{ $user->addr1 }}">
                                    <label for="addr1" class="control-label">기본주소</label>
                                <input id="addr2" type="text" class="" name="addr2" value="{{ $user->addr2 }}">
                                    <label for="addr2" class="control-label">상세주소</label>
                                <input id="addr3" type="text" class="" name="addr3" value="{{ $user->addr3 }}">
                                    <label for="addr3" class="control-label">참고항목</label>
                                @if ($errors->has('addr1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('addr1') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endif

                        <div class="panel-heading">기타 개인설정</div>

                        @if(config('gnu.signature') == 1)
                        <div class="form-group{{ $errors->has('signature') ? ' has-error' : '' }}">
                            <label for="signature" class="col-md-4 control-label">서명</label>

                            <div class="col-md-6">
                                <textarea name="signature" class="form-control">{{ $user->signature }}</textarea>

                                @if ($errors->has('signature'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('signature') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if(config('gnu.profile') == 1)
                        <div class="form-group{{ $errors->has('profile') ? ' has-error' : '' }}">
                            <label for="profile" class="col-md-4 control-label">자기소개</label>

                            <div class="col-md-6">
                                <textarea name="profile" class="form-control">{{ $user->profile }}</textarea>

                                @if ($errors->has('profile'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('profile') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endif

                        <div class="form-group{{ $errors->has('mailing') ? ' has-error' : '' }}">
                            <label for="mailing" class="col-md-4 control-label">메일링서비스</label>

                            <div class="col-md-6">
                                <input id="mailing" type="checkbox" name="mailing" value="1" @if($user->mailing == 1) checked @endif>
                                정보 메일을 받겠습니다.

                                @if ($errors->has('mailing'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('mailing') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('sms') ? ' has-error' : '' }}">
                            <label for="sms" class="col-md-4 control-label">SMS 수신여부</label>

                            <div class="col-md-6">
                                <input id="sms" type="checkbox" name="sms" value="1" @if($user->sms == 1) checked @endif>
                                휴대폰 문자메세지를 받겠습니다.

                                @if ($errors->has('sms'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('sms') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('open') ? ' has-error' : '' }}">
                            <label for="open" class="col-md-4 control-label">정보공개</label>

                            <div class="col-md-6">
                                <p>
                                    정보공개를 바꾸시면 {{ config('gnu.openDate') }}일 이내에는 변경이 안됩니다.
                                </p>
                                <input id="open" type="checkbox" name="open" value="1" @if($user->open == 1) checked @endif>
                                다른분들이 나의 정보를 볼 수 있도록 합니다.

                                @if ($errors->has('open'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('open') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('recommend') ? ' has-error' : '' }}">
                            <label for="recommend" class="col-md-4 control-label">추천인아이디</label>

                            <div class="col-md-6">
                                <input id="recommend" type="text" class="form-control" name="recommend" value="{{ $user->recommend }}">

                                @if ($errors->has('recommend'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('recommend') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    변경하기
                                </button>
                                <a class="btn btn-primary" href="{{ route('/') }}">취소</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
