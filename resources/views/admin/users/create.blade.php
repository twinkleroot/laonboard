@extends('theme')

@section('title')
    회원 추가 | LaBoard
@endsection

@section('include_script')
    <script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
    <script src="{{ url('js/postcode.js') }}"></script>
@endsection

@section('content')
<div>
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
                    </table>
                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-5">
                            <button type="submit" class="btn btn-primary">
                                확인
                            </button>
                            <a class="btn btn-primary" href="{{ route('admin.users.index') }}">목록</a>
                        </div>
                    </div>
                </form>
            </div>
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
