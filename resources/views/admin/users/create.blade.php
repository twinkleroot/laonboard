@extends('admin.admin')

@section('title')
    회원 추가 | {{ $title }}
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
        <h3>회원추가</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">회원관리</li>
            <li class="depth">회원추가</li>
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
                <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.users.store') }}">
                    {{ csrf_field() }}
                    <section id="mb_basic" class="first">
                        <div class="st_title">기본 회원정보</div>
                        <div class="st_contents">
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">이메일</label>
                                <div class="col-md-5 @if($errors->get('email')) has-error @endif ">
                                    <input type="email" class="form-control" id="">
                                </div>
                                <div class="col-md-5" style="padding-left: 0;">
                                    <span class="btn btn-default" style="font-size: 12px; line-height: 20px;">접근가능그룹보기</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">비밀번호</label>
                                <div class="col-md-5">
                                    <input type="password" class="form-control" id="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">닉네임</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">회원권한</label>
                                <div class="col-md-3">
                                    <select class="form-control">
                                        <option>1</option>
                                        <option>2</option>
                                        <option>3</option>
                                        <option>4</option>
                                        <option>5</option>
                                        <option selected>6</option>
                                        <option>7</option>
                                        <option>8</option>
                                        <option>9</option>
                                        <option>10</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">포인트</label>
                                <div class="col-md-3">
                                    <span style="display: inline-block; line-height: 30px; font-size: 12px;">300 점</span> <!-- 회원추가의 경우 기본 0점 -->
                                </div>
                            </div>
                            <!-- 회원추가의 경우 보이지 않음 -->
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">회원가입일</label>
                                <div class="col-md-3">
                                    <span style="display: inline-block; line-height: 30px; font-size: 12px;">2017-04-20 15:17:57</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">최근접속일</label>
                                <div class="col-md-3">
                                    <span style="display: inline-block; line-height: 30px; font-size: 12px;">2017-04-25 15:17:57</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">IP</label>
                                <div class="col-md-3">
                                    <span style="display: inline-block; line-height: 30px; font-size: 12px;">   106.245.92.30</span>
                                </div>
                            </div>
                            <!-- 회원추가의 경우 보이지 않음 END -->

                            <!--
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">회원상태</label>
                                <div class="col-md-3">
                                    <select class="form-control">
                                        <option>정상</option>
                                        <option>차단</option>
                                        <option>탈퇴</option>
                                    </select>
                                </div>
                            </div>
                            -->
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">탈퇴일자</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="">
                                </div>
                                <div class="col-md-3">
                                    <input type="checkbox" value=""> 탈퇴일을 오늘로 지정
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">접근차단일자</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="">
                                </div>
                                <div class="col-md-3">
                                    <input type="checkbox" value=""> 차단일을 오늘로 지정
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
                                    <input type="text" class="form-control" id="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">전화번호</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">휴대폰번호</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">주소</label>
                                <div class="col-md-5 row mb10">
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" id="" placeholder="우편번호">
                                    </div>
                                    <div class="col-sm-7" style="padding-left: 0;">
                                        <button class="btn btn-default" style="font-size: 12px; line-height: 20px;">주소검색</button>
                                    </div>
                                </div>
                                <div class="col-md-5 col-md-offset-2 mb10">
                                    <label for="" class="sr-only">기본주소</label>
                                    <input type="text" class="form-control" id="" placeholder="기본주소">
                                </div>
                                <div class="col-md-5 col-md-offset-2">
                                    <label for="" class="sr-only">상세주소</label>
                                    <input type="text" class="form-control" id="" placeholder="상세주소">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">서명</label>
                                <div class="col-md-5">
                                    <textarea class="form-control" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">자기소개</label>
                                <div class="col-md-5">
                                    <textarea class="form-control" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">메모</label>
                                <div class="col-md-5">
                                    <textarea class="form-control" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">추천인</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="">
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="B">
                        <div class="st_title">부가설정</div>
                        <div class="st_contents">
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">메일 수신</label>
                                <div class="col-md-5">
                                    <label class="radio-inline">
                                        <input type="radio" name="1" id="" value="option1"> 예
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="1" id="" value="option2"> 아니오
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">SMS 수신</label>
                                <div class="col-md-5">
                                    <label class="radio-inline">
                                        <input type="radio" name="2" id="" value="option1"> 예
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="2" id="" value="option2"> 아니오
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">정보공개</label>
                                <div class="col-md-5">
                                    <label class="radio-inline">
                                        <input type="radio" name="3" id="" value="option1"> 예
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="3" id="" value="option2"> 아니오
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">회원아이콘</label>
                                <div class="col-md-5">
                                    <input type="file" id="exampleInputFile">
                                    <p class="help-block">이미지 크기는 넓이 22픽셀 높이 22픽셀로 해주세요.</p>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="C">
                        <div class="st_title">본인인증</div>
                        <div class="st_contents">
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">본인확인방법</label>
                                <div class="col-md-5">
                                    <label class="radio-inline">
                                        <input type="radio" name="4" id="" value="option1"> 아이핀
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="4" id="" value="option2"> 휴대폰
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">본인확인</label>
                                <div class="col-md-5">
                                    <label class="radio-inline">
                                        <input type="radio" name="5" id="" value="option1"> 예
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="5" id="" value="option2"> 아니오
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">성인인증</label>
                                <div class="col-md-5">
                                    <label class="radio-inline">
                                        <input type="radio" name="6" id="" value="option1"> 예
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="6" id="" value="option2"> 아니오
                                    </label>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="more">
                        <div class="st_title">여분필드</div>
                        <div class="st_contents">
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">여분 필드 1</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" id="">
                                </div>
                            </div>
                        </div>
                    </section>
                </form>
            </div>
        </div>

<!--
    <div class="row">
        <div class="col-md-12 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading">회원 추가</div>
                <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.users.store') }}">
                    {{ csrf_field() }}
                    <table class="table table-hover">
                        <tr>
                            <th>이메일</th>
                            <td @if($errors->get('email')) class="has-error" @endif>
                                <input type="text" class="form-control" name="email" value="{{ $user->email }}" required/>
                                @foreach ($errors->get('email') as $message)
                                    <span class="help-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @endforeach
                            </td>
                            <th>비밀번호</th>
                            <td @if($errors->get('password')) class="has-error" @endif>
                                <input type="password" class="form-control" name="password" value="" required/>
                                @foreach ($errors->get('password') as $message)
                                    <span class="help-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>이름</th>
                            <td @if($errors->get('name')) class="has-error" @endif>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}"/>
                                @foreach ($errors->get('name') as $message)
                                    <span class="help-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @endforeach
                            </td>
                            <th>닉네임</th>
                            <td @if($errors->get('nick')) class="has-error" @endif>
                                <input type="text" class="form-control" name="nick" value="{{ old('nick') }}" required/>
                                @foreach ($errors->get('nick') as $message)
                                    <span class="help-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>회원 권한</th>
                            <td>
                                <select class="form-control" name="level">
                                    @for($i=1;$i<=10;$i++)
                                    <option value={{ $i }} @if($config->joinLevel == $i) selected @endif>
                                        {{ $i }}
                                    </option>
                                    @endfor
                                </select>
                            </td>
                            <th>포인트</th>
                            <td><input type="text" class="form-control" name="point" value="{{ $config->joinPoint }}" /></td>
                        </tr>
                        <tr>
                            <th>홈페이지</th>
                            <td @if($errors->get('homepage')) class="has-error" @endif>
                                <input type="text" class="form-control" name="homepage" value="{{ old('homepage') }}" />
                                @foreach ($errors->get('homepage') as $message)
                                    <span class="help-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>휴대폰번호</th>
                            <td @if($errors->get('hp')) class="has-error" @endif>
                                <input type="text" class="form-control" name="hp" value="{{ old('hp') }}" />
                                @foreach ($errors->get('hp') as $message)
                                    <span class="help-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @endforeach
                            </td>
                            <th>전화번호</th>
                            <td @if($errors->get('tel')) class="has-error" @endif>
                                <input type="text" class="form-control" name="tel" value="{{ old('tel') }}" />
                                @foreach ($errors->get('tel') as $message)
                                    <span class="help-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>본인확인방법</th>
                            <td><input type="radio" name="certify_case" id="certify_case_ipin" value="0" />
                                    <label for="certify_case_ipin">아이핀</label>
                                <input type="radio" name="certify_case" id="certify_case_hp" value="1" />
                                    <label for="certify_case_hp">휴대폰</label>
                            </td>
                        </tr>
                        <tr>
                            <th>본인확인</th>
                            <td>
                                <input type="radio" name="certify" id="certify_yes" @if(old('certify')=='1') checked @endif value="1" />
                                    <label for="certify_yes">예</label>
                                <input type="radio" name="certify" id="certify_no" @if(old('certify')=='0') checked @endif value="0" />
                                    <label for="certify_no">아니오</label>
                            </td>
                            <th>성인인증</th>
                            <td>
                                <input type="radio" name="adult" id="adult_yes" @if(old('adult')=='1') checked @endif value="1" />
                                    <label for="adult_yes">예</label>
                                <input type="radio" name="adult" id="adult_no" @if(old('adult')=='0') checked @endif value="0" />
                                    <label for="adult_no">아니오</label>
                            </td>
                        </tr>
                        <tr>
                            <th>주소</th>
                            <td>
                                <input type="text" id="zip" name="zip" class="form-control" value="{{ old('zip') }}" placeholder="우편 번호">
                                <input type="button" onclick="execDaumPostcode()" value="주소 검색"><br>

                                <div id="wrap" style="display:none;border:1px solid;width:500px;height:300px;margin:5px 0;position:relative">
                                    <img src="//t1.daumcdn.net/localimg/localimages/07/postcode/320/close.png"
                                        style="cursor:pointer;position:absolute;right:0px;top:-1px;z-index:1"
                                         id="btnFoldWrap" onclick="foldDaumPostcode()" alt="접기 버튼">
                                </div>
                                <input type="text" id="addr1" name="addr1" class="form-control" value="{{ old('addr1') }}" placeholder="기본 주소">
                                <input type="text" id="addr2" name="addr2" class="form-control" value="{{ old('addr2') }}" placeholder="상세 주소">
                            </td>
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
                                <input type="radio" name="mailing" id="mailing_yes" @if(old('mailing')=='1') checked @endif value="1" />
                                    <label for="mailing_yes">예</label>
                                <input type="radio" name="mailing" id="mailing_no" @if(old('mailing')=='0') checked @endif value="0" />
                                    <label for="mailing_no">아니오</label>
                            </td>
                            <th>SMS 수신</th>
                            <td>
                                <input type="radio" name="sms" id="sms_yes" @if(old('sms')=='1') checked @endif value="1" />
                                    <label for="sms_yes">예</label>
                                <input type="radio" name="sms" id="sms_no" @if(old('sms')=='0') checked @endif value="0" />
                                    <label for="sms_no">아니오</label>
                            </td>
                        </tr>
                        <tr>
                            <th>정보 공개</th>
                            <td>
                                <input type="radio" name="open" id="open_yes" @if(old('open')=='1') checked @endif value="1" />
                                    <label for="open_yes">예</label>
                                <input type="radio" name="open" id="open_no" @if(old('open')=='0') checked @endif value="0" />
                                    <label for="open_no">아니오</label>
                            </td>
                        </tr>
                        <tr>
                            <th>서명</th>
                            <td>
                                <textarea name="signature" class="form-control">{{ old('signature') }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>자기 소개</th>
                            <td>
                                <textarea name="profile" class="form-control">{{ old('profile') }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>메모</th>
                            <td>
                                <textarea name="memo" class="form-control">{{ old('memo') }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>탈퇴일자</th>
                            <td>
                                <input type="text" class="form-control" name="leave_date" id="leave_date"
                                    value="" />
                                <input type="checkbox" name="leave_date_set_today" value="1" id="leave_date_set_today"
                                    onclick="setToday(this.form.leave_date_set_today, this.form.leave_date)"/>
                                <label for="leave_date_set_today">탈퇴일을 오늘로 지정</label>
                            </td>
                            <th>접근차단일자</th>
                            <td>
                                <input type="text" class="form-control" name="intercept_date" id="intercept_date"
                                    value="" />
                                <input type="checkbox" name="intercept_date_set_today" id="intercept_date_set_today" value="1"
                                    onclick="setToday(this.form.intercept_date_set_today, this.form.intercept_date)"/>
                                <label for="intercept_date_set_today">접근차단일을 오늘로 지정</label>
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드1</th>
                            <td>
                                <input type="text" name="extra_1" />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드2</th>
                            <td>
                                <input type="text" name="extra_2" />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드3</th>
                            <td>
                                <input type="text" name="extra_3" />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드4</th>
                            <td>
                                <input type="text" name="extra_4" />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드5</th>
                            <td>
                                <input type="text" name="extra_5" />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드6</th>
                            <td>
                                <input type="text" name="extra_6" />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드7</th>
                            <td>
                                <input type="text" name="extra_7" />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드8</th>
                            <td>
                                <input type="text" name="extra_8" />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드9</th>
                            <td>
                                <input type="text" name="extra_9" />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드10</th>
                            <td>
                                <input type="text" name="extra_10" />
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>-->
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
