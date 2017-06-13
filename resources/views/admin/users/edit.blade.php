@extends('admin.admin')

@section('title')
    회원 수정 | {{ $title }}
@endsection

@section('include_script')
    <script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
    <script src="{{ url('js/postcode.js') }}"></script>
    <script type="text/javascript">
        jQuery("document").ready(function($){
            var nav = $('.body-tab');
             
            $(window).scroll(function () {
                if ($(this).scrollTop() > 205) {
                    nav.addClass("f-tab");
                } else {
                    nav.removeClass("f-tab");
                }
            });
        });
    </script>
@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>회원수정</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">회원관리</li>
            <li class="depth">회원수정</li>
        </ul>
    </div>
</div>

<div class="body-contents">
    <div class="body-tab">
        <ul class="mb_menu">
            <li class="tab">
                <a href="#mb_basic">기본정보</a>
            </li>
            <li class="tab">
                <a href="#mb_add">추가정보</a>
            </li>
            <li class="tab">
                <a href="#B">부가설정</a>
            </li>
            <li class="tab">
                <a href="#C">본인인증</a>
            </li>
            <li class="tab">
                <a href="#more">여분필드</a>
            </li>
        </ul>
        <div class="pull-right">
            <ul class="mb_btn">
                <li>
                    <button type="submit" class="btn btn-default">확인</button>
                </li>
                <li>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-default" role="button">목록</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.users.update', $id) }}">
                {{ csrf_field() }}
                    <section id="mb_basic" class="first">
                        <div class="st_title">기본 회원정보</div>
                        <div class="st_contents">
                            <div class="form-group">
                                <label for="email" class="col-md-2 control-label">이메일</label>
                                <div class="col-md-5">
                                    <input type="email" class="form-control" name="email" value="{{ $user->email }}" readonly>
                                </div>
                                <div class="col-md-5" style="padding-left: 0;">
                                    <a href="#" class="btn btn-default form_btn" role="button">접근가능그룹보기</a>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="change_password" class="col-md-2 control-label">비밀번호</label>
                                <div class="col-md-5 @if($errors->get('password')) has-error @endif">
                                    <input type="password" class="form-control" name="change_password" value="">
                                    @foreach ($errors->get('password') as $message)
                                        <span class="help-block">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="name" class="col-md-2 control-label">이름</label>
                                <div class="col-md-3  @if($errors->get('name')) has-error @endif">
                                    <input type="text" class="form-control"  name="name" value="{{ $user->name }}">
                                    @foreach ($errors->get('name') as $message)
                                        <span class="help-block">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="nick" class="col-md-2 control-label">닉네임</label>
                                <div class="col-md-3 @if($errors->get('nick')) has-error @endif">
                                    <input type="text" class="form-control" name="nick" value="{{ $user->nick }}">
                                    @foreach ($errors->get('nick') as $message)
                                        <span class="help-block">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="level" class="col-md-2 control-label">회원권한</label>
                                <div class="col-md-3">
                                    <select name="level" class="form-control level">
                                    @for ($i=1; $i<=10; $i++)
                                        <option value='{{ $i }}' @if($user->level == $i) selected @endif>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">포인트</label>
                                <div class="col-md-3">
                                    <span class="form_txt">{{ $user->point }} 점</span> <!-- 회원추가의 경우 기본 0점 -->
                                </div>
                            </div>
                            <!-- 회원추가의 경우 보이지 않음 -->
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">회원가입일</label>
                                <div class="col-md-3">
                                    <span class="form_txt">@datetime($user->created_at)</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">최근접속일</label>
                                <div class="col-md-3">
                                    <span class="form_txt">{{ $user->today_login }}</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">IP</label>
                                <div class="col-md-3">
                                    <span class="form_txt">{{ $user->ip }}</span>
                                </div>
                            </div>
                            <!-- 회원추가의 경우 보이지 않음 END -->

                            
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">회원상태</label>
                                <div class="col-md-3">
                                    @if(!is_null($user->leave_date))
                                        <span class="mb_msg withdraw">탈퇴</span>
                                    @elseif (!is_null($user->intercept_date))
                                        <span class="mb_msg intercept">차단</span>
                                    @else
                                        <span class="mb_msg">정상</span>
                                    @endif
                                    <!--
                                    <select class="form-control">

                                        <option>정상</option>
                                        <option>차단</option>
                                        <option>탈퇴</option>
                                    </select>-->
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">탈퇴일자</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="leave_date" id="leave_date"
                                    value="{{ $user->leave_date }}">
                                </div>
                                <div class="col-md-3"> 
                                    <input type="checkbox" name="leave_date_set_today" value="1" id="leave_date_set_today"
                                    onclick="setToday(this.form.leave_date_set_today, this.form.leave_date)"/>
                                    <label for="leave_date_set_today">탈퇴일을 오늘로 지정</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">접근차단일자</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="intercept_date" id="intercept_date"
                                    value="{{ $user->intercept_date }}">
                                </div>
                                <div class="col-md-3">
                                    <input type="checkbox" name="intercept_date_set_today" id="intercept_date_set_today" value="1"
                                    onclick="setToday(this.form.intercept_date_set_today, this.form.intercept_date)"/>
                                <label for="intercept_date_set_today">접근차단일을 오늘로 지정</label>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="mb_add">
                        <div class="st_title">추가 회원정보</div>
                        <div class="st_contents">
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">홈페이지</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="homepage" value="{{ $user->homepage }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tel" class="col-md-2 control-label">전화번호</label>
                                <div class="col-md-4 @if($errors->get('tel')) has-error @endif">
                                    <input type="text" class="form-control" name="tel" value="{{ $user->tel }}">
                                    @foreach ($errors->get('tel') as $message)
                                        <span class="help-block">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="hp" class="col-md-2 control-label">휴대폰번호</label>
                                <div class="col-md-4 @if($errors->get('hp')) has-error @endif">
                                    <input type="text" class="form-control" name="hp" value="{{ $user->hp }}">
                                    @foreach ($errors->get('hp') as $message)
                                        <span class="help-block">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">주소</label>
                                <div class="col-md-5 row mb10">
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" id="zip" name="zip" value="{{ $user->zip }}" placeholder="@lang('messages.zip')">
                                    </div>
                                    <div class="col-sm-7" style="padding-left: 0;">
                                        <input type="button" class="btn btn-default form_btn" onclick="execDaumPostcode()" value="@lang('messages.address_search')">
                                    </div>

                                    <!-- 우편번호검색 -->
                                    <div id="wrap" style="display:none;border:1px solid;width:500px;height:300px;margin:5px 0;position:relative">
                                        <img src="//t1.daumcdn.net/localimg/localimages/07/postcode/320/close.png"
                                        style="cursor:pointer;position:absolute;right:0px;top:-1px;z-index:1"
                                         id="btnFoldWrap" onclick="foldDaumPostcode()" alt="접기 버튼">
                                    </div>

                                </div>
                                <div class="col-md-5 col-md-offset-2 mb10">
                                    <label for="" class="sr-only">기본주소</label>
                                    <input type="text" class="form-control" id="addr1" name="addr1" value="{{ $user->addr1 }}" placeholder="@lang('messages.address1')">
                                </div>
                                <div class="col-md-5 col-md-offset-2">
                                    <label for="" class="sr-only">상세주소</label>
                                    <input type="text" class="form-control" id="addr2" name="addr2" value="{{ $user->addr2 }}" placeholder="@lang('messages.address2')">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="signature" class="col-md-2 control-label">서명</label>
                                <div class="col-md-5">
                                    <textarea class="form-control" rows="5" name="signature">{{ $user->signature }}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="profile" class="col-md-2 control-label">자기소개</label>
                                <div class="col-md-5">
                                    <textarea class="form-control" rows="5" name="profile">{{ $user->profile }}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="memo" class="col-md-2 control-label">메모</label>
                                <div class="col-md-5">
                                    <textarea class="form-control" rows="5" name="memo">{{ $user->memo }}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">추천인</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="?">
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="B">
                        <div class="st_title">부가설정</div>
                        <div class="st_contents">
                            <div class="form-group">
                                <label for="mailing" class="col-md-2 control-label">메일 수신</label>
                                <div class="col-md-5">
                                    <input type="radio" name="mailing" id="mailing_yes" @if($user->mailing === 1) checked @endif value="1" />
                                        <label for="mailing_yes">예</label>
                                    <input type="radio" name="mailing" id="mailing_no" @if($user->mailing === 0) checked @endif value="0" />
                                        <label for="mailing_no">아니오</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="sms" class="col-md-2 control-label">SMS 수신</label>
                                <div class="col-md-5">
                                    <input type="radio" name="sms" id="sms_yes" @if($user->sms === 1) checked @endif value="1" />
                                        <label for="sms_yes">예</label>
                                    <input type="radio" name="sms" id="sms_no" @if($user->sms === 0) checked @endif value="0" />
                                        <label for="sms_no">아니오</label>
                                    </div>
                            </div>
                            <div class="form-group">
                                <label for="open" class="col-md-2 control-label">정보공개</label>
                                <div class="col-md-5">
                                    <input type="radio" name="open" id="open_yes" @if($user->open === 1) checked @endif value="1" />
                                        <label for="open_yes">예</label>
                                    <input type="radio" name="open" id="open_no" @if($user->open === 0) checked @endif value="0" />
                                        <label for="open_no">아니오</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">회원아이콘</label>
                                <div class="col-md-5">
                                    <input type="file" name="icon" value="">
                                    <p class="help-block">이미지 크기는 넓이 22픽셀 높이 22픽셀로 해주세요.</p>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="C">
                        <div class="st_title">본인인증</div>
                        <div class="st_contents">
                            <div class="form-group">
                                <label for="certify_case" class="col-md-2 control-label">본인확인방법</label>
                                <div class="col-md-5">
                                    <input type="radio" name="certify_case" id="certify_case_ipin" value="0" />
                                        <label for="certify_case_ipin">아이핀</label>
                                    <input type="radio" name="certify_case" id="certify_case_hp" value="1" />
                                        <label for="certify_case_hp">휴대폰</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="certify" class="col-md-2 control-label">본인확인</label>
                                <div class="col-md-5">
                                    <input type="radio" name="certify" id="certify_yes" @if($user->certify == 1) checked @endif value="1" />
                                        <label for="certify_yes">예</label>
                                    <input type="radio" name="certify" id="certify_no" @if($user->certify == 0 || empty($user->certify)) checked @endif value="0" />
                                        <label for="certify_no">아니오</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for=adult" class="col-md-2 control-label">성인인증</label>
                                <div class="col-md-5">
                                    <input type="radio" name="adult" id="adult_yes" @if($user->adult === 1) checked @endif value="1" />
                                        <label for="adult_yes">예</label>
                                    <input type="radio" name="adult" id="adult_no" @if($user->adult === 0) checked @endif value="0" />
                                        <label for="adult_no">아니오</label>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="more">
                        <div class="st_title">여분필드</div>
                        <div class="st_contents">
                            @for($i=0; $i<10; $i++)
                                <div class="form-group">
                                    <label for="" class="col-md-2 control-label">여분필드{{ $i }}</label>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control"  name="extra_{{ $i }}" value="{{ $user['extra_'. $i] }}">
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </section>
                </form>
            </div>
        </div>
</div>
<script>
function setToday(chkbox, place) {
    var now = new Date();

    if(chkbox.checked) {
        $(place).val(getFormattedDate(now));
    } else {
        $(place).val('');
    }
}
function getFormattedDate(date) {
    var year = date.getFullYear();
    var month = (1 + date.getMonth()).toString();
    var day = date.getDate().toString();

    month = month.length > 1 ? month : '0' + month;
    day = day.length > 1 ? day : '0' + day;

    return year + month + day;
}
</script>
@endsection
