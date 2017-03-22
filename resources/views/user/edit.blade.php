@extends('layouts.app')

@section('title')
    LaBoard | 회원 정보 수정
@endsection

@section('include_script')
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
    <script src="{{ url('js/postcode.js') }}"></script>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <script src="{{ asset('js/postcode.js') }}"></script>
                <div class="panel-heading">회원 정보 수정</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('user.update') }}">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}
                        <div class="panel-heading">사이트 이용정보 입력</div>

                        <div class="form-group">
                            <label for="email_readonly" class="col-md-4 control-label">이메일</label>
                            <div class="col-md-6">
                                <input type="email" class="form-control" name="email_readonly" value="{{ $user->email }}" readonly>
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
                            <label for="password_confirmation" class="col-md-4 control-label">비밀번호 확인</label>
                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        @if($config->name == 1
                            or $config->homepage == 1
                            or $config->tel == 1
                            or $config->hp == 1
                            or $config->addr == 1)
                        <div class="panel-heading">개인정보 입력</div>
                        @endif
                        @if($config->name == 1)
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">이름</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ $user->name }}">

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($nickChangable)
                        <div class="form-group{{ $errors->has('nick') ? ' has-error' : '' }}">
                            <label for="nick" class="col-md-4 control-label">닉네임</label>

                            <div class="col-md-6">
                                <p>
                                    공백없이 한글, 영문, 숫자만 입력 가능 <br />
                                    (한글2자, 영문4자 이상)<br />
                                    닉네임을 바꾸시면 {{ $config->nickDate }}일 이내에는 변경할 수 없습니다.
                                </p>
                                <input id="nick" type="text" class="form-control" name="nick" value="{{ $user->nick }}" required autofocus>

                                @if ($errors->has('nick'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nick') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($config->homepage == 1)
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

                        @if($config->tel == 1)
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

                        @if($config->hp == 1)
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

                        @if($config->addr == 1)
                        <div class="form-group">
                            <label for="addr1" class="col-md-4 control-label">주소</label>
                            <div class="col-md-6">

                                <input type="text" id="zip" name="zip" class="form-control" value="{{ $user->zip }}" placeholder="우편번호">
                                <input type="button" onclick="execDaumPostcode()" value="주소 검색"><br>

                                <div id="wrap" style="display:none;border:1px solid;width:500px;height:300px;margin:5px 0;position:relative">
                                    <img src="//t1.daumcdn.net/localimg/localimages/07/postcode/320/close.png"
                                        style="cursor:pointer;position:absolute;right:0px;top:-1px;z-index:1"
                                         id="btnFoldWrap" onclick="foldDaumPostcode()" alt="접기 버튼">
                                </div>
                                <input type="text" id="addr1" name="addr1" class="form-control" value="{{ $user->addr1 }}" placeholder="기본 주소">
                                <input type="text" id="addr2" name="addr2" class="form-control" value="{{ $user->addr2 }}" placeholder="나머지 주소">
                            </div>
                        </div>
                        @endif

                        <div class="panel-heading">기타 개인 설정</div>
                        
                        @if($config->signature == 1)
                        <div class="form-group">
                            <label for="signature" class="col-md-4 control-label">서명</label>

                            <div class="col-md-6">
                                <textarea name="signature" class="form-control">{{ $user->signature }}</textarea>
                            </div>
                        </div>
                        @endif

                        @if($config->profile == 1)
                        <div class="form-group">
                            <label for="profile" class="col-md-4 control-label">자기소개</label>

                            <div class="col-md-6">
                                <textarea name="profile" class="form-control">{{ $user->profile }}</textarea>
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

                            @if($openChangable)
                                <div class="col-md-6">
                                    <p>
                                        정보공개를 바꾸시면 {{ $config->openDate }}일 이내에는 변경이 안됩니다.
                                    </p>
                                    <input id="open" type="checkbox" name="open" value="1" @if($user->open == 1) checked @endif>
                                    다른분들이 나의 정보를 볼 수 있도록 합니다.

                                    @if ($errors->has('open'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('open') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            @else
                                <div class="col-md-6">
                                    <p>
                                        정보공개는 수정후 {{ $config->openDate }}일 이내,
                                        {{ $dueDate->year }}년
                                        {{ $dueDate->month }}월
                                        {{ $dueDate->day }}일 까지는 변경이 안됩니다.<br />
                                        이렇게 하는 이유는 잦은 정보공개 수정으로 인하여 쪽지를 보낸 후 받지 않는 경우를 막기 위해서 입니다.
                                    </p>
                                </div>
                            @endif
                        </div>

                        @if($config->recommend == 1)
                        <div class="form-group{{ $errors->has('recommend') ? ' has-error' : '' }}">
                            <label for="recommend" class="col-md-4 control-label">추천인 닉네임</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="recommend" value="{{  $recommend }}">

                                @if ($errors->has('recommend'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('recommend') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endif
                        <div class="form-group{{ $errors->has('reCapcha') ? ' has-error' : '' }}">
                            <div class="g-recaptcha col-md-6" data-sitekey="6LcKohkUAAAAANcgIst0HFMMT81Wq5HIxpiHhXGZ">
                            </div>
                            @if ($errors->has('reCapcha'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('reCapcha') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    변경하기
                                </button>
                                <a class="btn btn-primary" href="{{ route('index') }}">취소</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
