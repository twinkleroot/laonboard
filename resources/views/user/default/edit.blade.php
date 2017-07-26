@extends('layout.'. cache('config.skin')->layout. '.basic')

@section('title')
    회원 정보 수정 | {{ cache("config.homepage")->title }}
@endsection

@section('include_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/auth.css') }}">
@endsection

@section('include_script')
    <script src='https://www.google.com/recaptcha/api.js' async defer></script>
    <script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
    <script src="{{ url('js/postcode.js') }}"></script>
    <script src="{{ url('js/certify.js') }}"></script>
@endsection

@section('content')
@if($errors->has('message'))
    <div class="alert alert-info">
    {{ $errors->first('message') }}
    </div>
@endif
<div class="container">
<div class="row">
<div class="col-md-6 col-md-offset-3 col-xs-12">

<!-- user edit -->
    <div class="panel panel-default">
        <script src="{{ asset('js/postcode.js') }}"></script>
        <div class="panel-heading bg-sir">
            <h3 class="panel-title">회원 정보 수정</h3>
        </div>
        <div class="panel-body row">
            <form class="contents col-md-10 col-md-offset-1" role="form" id="userForm" name="userForm" method="POST" action="{{ route('user.update') }}" enctype="multipart/form-data" autocomplete="off">
                @if(cache('config.cert')->certHp || cache('config.cert')->certIpin)
                <input type="hidden" name="certType" value="">
                @if(!$config->name)
                <input type="hidden" name="name" value="">
                @endif
                @if(!$config->hp)
                <input type="hidden" name="hp" value="">
                @endif
                <input type="hidden" name="certNo" value="">
                @endif
                {{ csrf_field() }}
                {{ method_field('PUT') }}

                <div class="panel-heading">
                    <p class="heading-p">
                        <span class="heading-span">사이트 이용정보 입력</span>
                    </p>
                </div>

                <div class="form-group">
                    <label for="email_readonly">이메일</label>
                    <input type="email" class="form-control" name="email" value="{{ $user->email }}">
                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="password" class="control-label">비밀번호</label>
                    <input id="password" type="password" class="form-control" name="password" required>
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="control-label">비밀번호 확인</label>
                    <input type="password" class="form-control" name="password_confirmation" required>
                </div>

                @if($nickChangable)
                    <div class="form-group{{ $errors->has('nick') ? ' has-error' : '' }}">
                        <label for="nick" class="control-label">닉네임</label>

                        {{-- <div class="col-md-6"> --}}
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
                        {{-- </div> --}}
                    </div>
                @endif

                @if($config->name or $config->homepage or $config->tel or $config->hp or $config->addr)
                    <div class="panel-heading">
                        <p class="heading-p">
                            <span class="heading-span">개인정보 입력</span>
                        </p>
                    </div>
                @endif

                @if((cache('config.cert')->certUse && cache('config.cert')->certHp) || $config->name) <!-- 이름 -->
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="name" class="control-label">이름</label>
                        @if(cache('config.cert'))
                        <p>
                            아이핀 본인확인 후에는 이름이 자동 입력되고 휴대폰 본인확인 후에는 이름과 휴대폰번호가 자동 입력되어 수동으로 입력할수 없게 됩니다.
                        </p>
                        @endif
                        <input id="name" type="text" class="form-control" name="name" value="{{ $user->name }}" @if($user->certify) readonly @endif>
                        @if($user->certify)
                        <div class="helpbox bg-danger">
                            <p>휴대폰 본인확인 및 성인인증 완료</p>
                        </div>
                        @endif
                        @if ($errors->has('name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                    </div>
                @endif

                @if($config->homepage) <!-- 홈페이지 -->
                    <div class="form-group{{ $errors->has('homepage') ? ' has-error' : '' }}">
                        <label for="homepage" class="control-label">홈페이지</label>

                        <input id="homepage" type="text" class="form-control" name="homepage" value="{{ $user->homepage }}">

                            @if ($errors->has('homepage'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('homepage') }}</strong>
                                </span>
                            @endif
                    </div>
                @endif

                @if($config->tel) <!-- 전화번호 -->
                    <div class="form-group{{ $errors->has('tel') ? ' has-error' : '' }}">
                        <label for="tel" class="control-label">전화번호</label>

                        <input id="tel" type="text" class="form-control" name="tel" value="{{ $user->tel }}">

                            @if ($errors->has('tel'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('tel') }}</strong>
                                </span>
                            @endif
                    </div>
                @endif

                @if( (cache('config.cert')->certUse && cache('config.cert')->certHp) || $config->hp) <!-- 휴대폰번호 -->
                    <div class="form-group">
                        <label for="hp" class="control-label">휴대폰번호</label>

                        <div class="form-group row">
                            <div class="col-xs-8">
                                <input id="hp" type="text" class="form-control" name="hp" value="{{ $user->hp }}">
                            </div>
                            @if(cache('config.cert')->certHp)
                            <div class="col-xs-4">
                                <input type="button" class="btn btn-block btn-sir" id="win_hp_cert" style="height: 40px;" value="휴대폰 본인확인">
                            </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if($config->addr) <!-- 주소 -->
                    <div class="form-group">
                        <label for="addr1" class="control-label">주소</label>

                        <div class="form-group row">
                            <div class="col-xs-8">
                                <input type="text" id="zip" name="zip" class="form-control" value="{{ $user->zip }}" placeholder="우편번호">
                            </div>
                            <div class="col-xs-4">
                            <input type="button" class="btn btn-block btn-sir" style="height: 40px;" onclick="execDaumPostcode()" value="주소 검색">
                            </div>
                        </div>

                        <div id="wrap" style="display:none;border:1px solid;width:500px;height:300px;margin:5px 0;position:relative">
                            <img src="//t1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" style="cursor:pointer;position:absolute;right:0px;top:-1px;z-index:1" id="btnFoldWrap" onclick="foldDaumPostcode()" alt="접기 버튼">
                        </div>

                        <div class="form-group">
                            <input type="text" id="addr1" name="addr1" class="form-control" value="{{ $user->addr1 }}" placeholder="기본 주소">
                            <input type="text" id="addr2" name="addr2" class="form-control" value="{{ $user->addr2 }}" placeholder="나머지 주소">
                        </div>
                    </div>
                @endif

                <div class="panel-heading">
                    <p class="heading-p">
                        <span class="heading-span">기타 개인 설정</span>
                    </p>
                </div>

                @if($config->signature) <!-- 서명 -->
                    <div class="form-group">
                        <label for="signature" class="control-label">서명</label>
                        <textarea name="signature" class="form-control">{{ $user->signature }}</textarea>
                    </div>
                @endif

                @if($config->profile) <!-- 자기소개 -->
                    <div class="form-group">
                        <label for="profile" class="control-label">자기소개</label>
                        <textarea name="profile" class="form-control">{{ $user->profile }}</textarea>
                    </div>
                @endif

                @if(cache('config.join')->useMemberIcon && $user->level >= cache('config.join')->iconLevel)
                <div class="form-group row {{ $errors->has('icon') ? ' has-error' : '' }}">
                    <label for="icon" class="col-xs-12 control-label">회원아이콘</label>

                    <div class="col-xs-12">
                        <p>
                            이미지 크기는 가로 {{ cache('config.join')->memberIconWidth }}픽셀, 세로 {{ cache('config.join')->memberIconHeight }}픽셀 이하로 해주세요.<br>
                            gif만 가능하며 용량 {{ cache('config.join')->memberIconSize }}바이트 이하만 등록됩니다.
                        </p>
                        <input id="icon" type="file" name="icon">
                        @if(File::exists($iconPath))
                        <img src="{{ $iconUrl }}" alt="회원아이콘">
                        <input type="checkbox" name="delIcon" value="1" id="delIcon">
                        <label for="delIcon">삭제</label>
                        @endif
                        @if ($errors->has('icon'))
                            <span class="help-block">
                                <strong>{{ $errors->first('icon') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                @endif

                <div class="form-group row {{ $errors->has('mailing') ? ' has-error' : '' }}">
                    <label for="mailing" class="col-xs-12 control-label">메일링서비스</label>

                    <div class="col-xs-12">
                        <input id="mailing" type="checkbox" name="mailing" value="1" @if($user->mailing == 1) checked @endif>
                        정보 메일을 받겠습니다.
                        @if ($errors->has('mailing')) <!-- 메일링 동의 -->
                            <span class="help-block">
                                <strong>{{ $errors->first('mailing') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row {{ $errors->has('sms') ? ' has-error' : '' }}">
                    <label for="sms" class="col-xs-12 control-label">SMS 수신여부</label>

                    <div class="col-xs-12">
                        <input id="sms" type="checkbox" name="sms" value="1" @if($user->sms == 1) checked @endif>
                            휴대폰 문자메세지를 받겠습니다.

                        @if ($errors->has('sms')) <!-- 문자수신 동의 -->
                            <span class="help-block">
                                <strong>{{ $errors->first('sms') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row {{ $errors->has('open') ? ' has-error' : '' }}">
                    <label for="open" class="col-xs-12 control-label">정보공개</label>

                    <div class="col-xs-12">
                        @if($openChangable) <!-- 정보공개 여부 -->
                            <input id="open" type="checkbox" name="open" value="1" @if($user->open == 1) checked @endif>
                                다른분들이 나의 정보를 볼 수 있도록 합니다.

                            <div class="helpbox bg-danger">
                                <p>정보공개를 바꾸시면 {{ $openDate }}일 이내에는 변경이 안됩니다.</p>
                            </div>

                            @if ($errors->has('open'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('open') }}</strong>
                                </span>
                            @endif

                        @else <!-- 정보공개 기간제한으로 인해 수정불가 안내 -->
                            <div class="helpbox bg-danger">
                                <p>
                                    정보공개는 수정후 {{ $openDate }}일 이내,
                                    {{ $dueDate->year }}년
                                    {{ $dueDate->month }}월
                                    {{ $dueDate->day }}일 까지는 변경이 안됩니다.
                                    이렇게 하는 이유는 잦은 정보공개 수정으로 인하여 쪽지를 보낸 후 받지 않는 경우를 막기 위해서 입니다.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                @if($config->recommend) <!-- 추천인 -->
                    <div class="form-group row {{ $errors->has('recommend') ? ' has-error' : '' }}">
                        <label for="recommend" class="col-xs-12 control-label">추천인 닉네임</label>

                        <div class="col-xs-12">
                            <input type="text" class="form-control" name="recommend" value="{{ $recommend ? : old('recommend') }}">
                        </div>

                    </div>
                @endif

                <div class="form-group row">
                    <label for="recommend" class="col-xs-12 control-label">소셜 계정 연결</label>
                    <div class="col-xs-12 social-login social_login_container">
                        @foreach($socials as $key => $value)
                            <a href="{{ $value == '' ? route('social', $key) : route('user.disconnectSocialAccount') }}"
                                id="{{ $key }}_social_link" class="btn btn-block btn-{{ $key }} social_link" data-provider="{{ $key }}">
                                <input type="hidden" data-key="{{ $key }}" name="social_id[]" class="social_id" value="{{ $value }}" />
                                <div class="icon icon-{{ $key }} @if($value != '') unlink @endif"></div>
                                <span class="text-left">{{ title_case($key) }} 연결 {{ $value == '' ? '' : '해제' }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <button type="button" class="btn btn-sir" onclick="validate();">변경하기</button>
                    <a href="{{ route('home') }}" class="btn btn-sir">취소</a>
                </div>
                <div id='recaptcha' class="g-recaptcha"
                    data-sitekey="{{ env('GOOGLE_INVISIBLE_RECAPTCHA_KEY') }}"
                    data-callback="onSubmit"
                    data-size="invisible" style="display:none">
                </div>
            </form>
        </div>
        </div>
</div>
</div>
</div>
<script>
function onSubmit(token) {
    $("#userForm").submit();
}
function validate(event) {
    grecaptcha.execute();
}
$(function(){

    // 아이핀인증
    $("#win_ipin_cert").click(function() {
        if(!cert_confirm())
            return false;

        var url = "http://ahn13.gnutest.com/gnu5/plugin/okname/ipin1.php";
        {{-- var url = "{{ route('cert.kcb.ipin') }}"; --}}
        certify_win_open('kcb-ipin', url);
        return;
    });

    // 휴대폰인증
    $("#win_hp_cert").click(function() {
        if(!cert_confirm())
            return false;

        @if(cache('config.cert')->certHp == 'kcb')
            certify_win_open("kcb-hp", "{{ route('cert.kcb.hp1')}}");
        @endif

        return;
    });

    var socials = [];

    $(".social_id").each(function(i, obj) {
        socials[$(obj).attr('data-key')] = $(obj).val();
    });

    $(".social_login_container").on("click", ".social_link", function(e) {
        e.preventDefault();

        var othis = $(this),
            $div_class = $(this).children("div").attr('class');

        if( $div_class.indexOf('unlink') >  0 ) {     //소셜계정 해제하기
            if(!confirm('정말 이 계정 연결을 해제하시겠습니까?')) {
                return false;
            }

            var ajax_url = $(this).attr('href');
            var provider = $(this).attr('data-provider');

            if(!provider){
                alert('잘못된 요청! provider 값이 없습니다.');
                return false;
            }

            $.ajax({
                url: ajax_url,
                type: 'POST',
                data: {
                    'provider' : provider,
                    'social_id' : (typeof socials[provider] != 'undefined') ? socials[provider] : '',
                    'user_id' : {{ Auth::user()->id }},
                    '_token' : $('input[name=_token]').val()
                },
                dataType: 'json',
                cache : false,
                async: false,
                success: function(data) {
                    if (data.error) {
                            alert(data.error);
                            return false;
                    } else {
                        var link_href = '/social/' + provider;
                        var str = provider.charAt(0).toUpperCase() + provider.slice(1) + ' 연결';

                        othis.attr({"href":link_href});
                        othis.children("div").removeClass("unlink").addClass("link");
                        othis.children("span").text(str);
                    }
                },
                error: function(data) {
                    try { console.log(data) } catch (e) { alert(data.error) };
                }
            });
        } else {        //소셜계정 연결하기
            var pop_url = $(this).attr("href");
            var is_popup = "1";

            if( is_popup ){
               var newWin = window.open(
                   pop_url,
                   "social_sing_on",
                   "location=0,status=0,scrollbars=0,width=600,height=500"
               );

               if(!newWin || newWin.closed || typeof newWin.closed=='undefined') {
                    alert('브라우저에서 팝업이 차단되어 있습니다. 팝업 활성화 후 다시 시도해 주세요.');
                }
            } else {
               location.replace(pop_url);
            }
        }

    });
});
</script>
@endsection
