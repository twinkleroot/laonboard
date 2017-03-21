@extends('themes.default.basic')
@section('title')
    회원가입
@endsection

@section('content')
<div class="container">
<div class="row">
<!-- auth register -->
<div class="col-md-6 col-md-offset-3">
    <div id="auth">
        <div class="header">
            <h1>회원가입</h1>
        </div>
        <form class="form-horizontal" role="form" method="POST" action="{{ route('register') }}">
            {{ csrf_field() }}

            <div class="form-box">
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label>
                        <span class="sr-only">이메일</span>
                        <input id="email" class="form-control col-xs-12" type="email" name="email" value="{{ old('email') }}" placeholder="이메일을 입력하세요" required autofocus="">
                    </label>

                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label>
                        <span class="sr-only">비밀번호</span>
                        <input id="password" class="form-control col-xs-12" type="password" name="password" placeholder="비밀번호를 입력하세요" required>
                    </label>

                    @if ($errors->has('password'))
                        <span class="help-block">
                          <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <label>
                        <span class="sr-only">비밀번호 확인</span>
                        <input id="password-confirm" class="form-control col-xs-12" type="password" name="password_confirmation" placeholder="비밀번호를 다시 입력하세요" required>
                    </label>
                </div>

                <div class="form-group{{ $errors->has('nick') ? ' has-error' : '' }}">
                    <label>
                        <span class="sr-only">닉네임</span>
                        <input id="nick" class="form-control col-xs-12" type="text" name="nick" value="{{ old('nick') }}" placeholder="닉네임을 입력하세요" required autofocus>
                    </label>

                    @if ($errors->has('nick'))
                        <span class="help-block">
                            <strong>{{ $errors->first('nick') }}</strong>
                        </span>
                    @endif

                    <p class="form-comment">
                        공백없이 한글, 영문, 숫자만 입력 가능<br>
                        (한글2자, 영문4자 이상)<br>
                        닉네임을 바꾸시면 0일 이내에는 변경할 수 없습니다
                    </p>
                </div>

            </div>

            {{--
            <div class="form-box">
                <div class="form-heading">개인정보 입력</div>

                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label>
                        <span class="sr-only">이름</span>
                        <input id="name" class="form-control col-xs-12" type="text" name="name" value="{{ old('name') }}" placeholder="이름을 입력하세요" required>
                    </label>

                    @if ($errors->has('name'))
                        <span class="help-block">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>

                @if(config('gnu.homepage') == 1)
                <div class="form-group{{ $errors->has('homepage') ? ' has-error' : '' }}">
                    <label>
                        <span class="sr-only">홈페이지</span>
                        <input id="homepage" class="form-control col-xs-12" type="text" name="homepage"  value="{{ old('homepage') }}" placeholder="홈페이지 주소를 입력하세요">
                    </label>

                    @if ($errors->has('homepage'))
                        <span class="help-block">
                            <strong>{{ $errors->first('homepage') }}</strong>
                        </span>
                    @endif
                </div>
                @endif

                @if(config('gnu.tel') == 1)
                <div class="form-group{{ $errors->has('tel') ? ' has-error' : '' }}">
                    <label>
                        <span class="sr-only">전화번호</span>
                        <input id="tel" class="form-control col-xs-12" type="text" name="tel" value="{{ old('tel') }}" placeholder="전화번호를 입력하세요">
                    </label>
                </div>
                @endif

                @if(config('gnu.hp') == 1)
                <div class="form-group{{ $errors->has('hp') ? ' has-error' : '' }}">
                    <label>
                        <span class="sr-only">휴대폰번호</span>
                        <input  id="hp" class="form-control col-xs-12" type="text" name="hp" value="{{ old('hp') }}" placeholder="휴대폰번호를 입력하세요">
                    </label>

                    @if ($errors->has('hp'))
                        <span class="help-block">
                            <strong>{{ $errors->first('hp') }}</strong>
                        </span>
                    @endif
                </div>
                @endif

                @if(config('gnu.addr') == 1)
                <div class="form-group{{ $errors->has('addr1') ? ' has-error' : '' }}">
                    <input id="reg_zip" type="text" class="form-control col-xs-9" name="zip" value="{{ old('zip') }}" placeholder="우편번호">
                    <button class="form-control col-xs-3">주소 검색</button>
                    <label>
                        <span class="sr-only">주소</span>
                        <input  id="addr1" class="form-control col-xs-12" type="text" name="addr1" value="{{ old('addr1') }}" placeholder="기본주소">
                    </label>
                    <input  id="addr2" class="form-control col-xs-12" type="text" name="addr2" value="{{ old('addr2') }}" placeholder="상세주소">
                    <input  id="addr3" class="form-control col-xs-12" type="text" name="addr3" value="{{ old('addr3') }}" placeholder="참고항목">
                    @if ($errors->has('addr1'))
                        <span class="help-block">
                            <strong>{{ $errors->first('addr1') }}</strong>
                        </span>
                    @endif
                </div>
                @endif
            </div>

            <div class="form-box">
                <div class="form-heading">기타 개인설정</div>

                @if(config('gnu.signature') == 1)
                <div class="form-group{{ $errors->has('signature') ? ' has-error' : '' }}"">
                    <label>
                        <span class="sr-only">서명</span>
                        <textarea id="signature" class="form-control col-xs-12" name="signature" placeholder="서명을 입력하세요">{{ old('signature' )}}</textarea>
                    </label>

                    @if ($errors->has('signature'))
                        <span class="help-block">
                            <strong>{{ $errors->first('signature') }}</strong>
                        </span>
                    @endif
                </div>
                @endif

                @if(config('gnu.profile') == 1)
                <div class="form-group{{ $errors->has('profile') ? ' has-error' : '' }}">
                    <label>
                        <span class="sr-only">자기소개</span>
                        <textarea id="profile" class="form-control col-xs-12" name="profile" placeholder="자기소개를 입력하세요">{{ old('profile' )}}</textarea>
                    </label>

                    @if ($errors->has('profile'))
                        <span class="help-block">
                            <strong>{{ $errors->first('profile') }}</strong>
                        </span>
                    @endif
                </div>
                @endif
            </div>

            <div class="form-box">
                <div class="form-heading">메일링 서비스</div>
                <div class="form-group{{ $errors->has('mailing') ? ' has-error' : '' }}">
                    <label>
                        <input id="mailing" type="checkbox" name="mailing" value="1">
                        <span class="check-span">정보 메일을 받겠습니다</span>
                    </label>

                    @if ($errors->has('mailing'))
                        <span class="help-block">
                            <strong>{{ $errors->first('mailing') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-box">
                <div class="form-heading">SMS 수신여부</div>
                <div class="form-group{{ $errors->has('sms') ? ' has-error' : '' }}">
                    <label>
                        <input id="sms" type="checkbox" name="sms" value="1">
                        <span class="check-span">휴대폰으로 문자메세지를 받겠습니다</span>
                    </label>

                    @if ($errors->has('sms'))
                        <span class="help-block">
                            <strong>{{ $errors->first('sms') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-box">
                <div class="form-heading">정보공개 여부</div>
                <div class="form-group{{ $errors->has('open') ? ' has-error' : '' }}">
                    <label>
                        <input id="open" type="checkbox" name="open" value="1">
                        <span class="check-span">다른 분들이 나의 정보를 볼 수 있도록 합니다.</span>
                    </label>

                    @if ($errors->has('open'))
                        <span class="help-block">
                            <strong>{{ $errors->first('open') }}</strong>
                        </span>
                    @endif

                    <p class="form-comment">정보공개 여부를 바꾸시면 0일 이내에는 변경할 수 없습니다</p>
                </div>
            </div>

            <div class="form-box">
                <div class="form-heading">추천인 아이디</div>
                <div class="form-group{{ $errors->has('recommend') ? ' has-error' : '' }}">
                    <label>
                        <span class="sr-only">추천인아이디</span>
                        <input id="recommend" class="form-control col-xs-12" type="text" name="recommend" value="{{ old('recommend') }}" placeholder="추천인아이디를 입력하세요">
                    </label>

                    @if ($errors->has('recommend'))
                        <span class="help-block">
                            <strong>{{ $errors->first('recommend') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
            --}}

            <div class="form-group">
                <button type="submit" class="join col-xs-12">회원가입</button>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection