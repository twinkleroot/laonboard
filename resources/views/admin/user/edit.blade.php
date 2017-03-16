@extends('layouts.app')

@section('title')
    LaBoard | 회원 수정
@endsection

@section('include_script')
    <script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
    <script src="{{ url('js/postcode.js') }}"></script>
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
                            <td @if($errors->get('password')) class="has-error" @endif>
                                <input type="password" name="change_password" class="form-control" value="" />
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
                                <input type="text" class="form-control" name="name" value="{{ $user->name }}" />
                                @foreach ($errors->get('name') as $message)
                                    <span class="help-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @endforeach
                            </td>
                            <th>닉네임</th>
                            <td @if($errors->get('nick')) class="has-error" @endif>
                                <input type="text" class="form-control" name="nick" value="{{ $user->nick }}" />
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
                                <select name='level' class='level'>
                                    @for ($i=1; $i<=10; $i++)
                                        <option value='{{ $i }}' @if($user->level == $i) selected @endif>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </td>
                            <th>포인트</th>
                            <td><input type="text" class="form-control" name="point" value="{{ $user->point }}" /></td>
                        </tr>
                        <tr>
                            <th>홈페이지</th>
                            <td><input type="text" class="form-control" name="homepage" value="{{ $user->homepage }}" /></td>
                        </tr>
                        <tr>
                            <th>휴대폰번호</th>
                            <td @if($errors->get('hp')) class="has-error" @endif>
                                <input type="text" class="form-control" name="hp" value="{{ $user->hp }}" />
                                @foreach ($errors->get('hp') as $message)
                                    <span class="help-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @endforeach
                            </td>
                            <th>전화번호</th>
                            <td @if($errors->get('tel')) class="has-error" @endif>
                                <input type="text" class="form-control" name="tel" value="{{ $user->tel }}" />
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
                                <input type="radio" name="certify" id="certify_yes" @if($user->certify == 1) checked @endif value="1" />
                                    <label for="certify_yes">예</label>
                                <input type="radio" name="certify" id="certify_no" @if($user->certify == 0 || empty($user->certify)) checked @endif value="0" />
                                    <label for="certify_no">아니오</label>
                            </td>
                            <th>성인인증</th>
                            <td>
                                <input type="radio" name="adult" id="adult_yes" @if($user->adult === 1) checked @endif value="1" />
                                    <label for="adult_yes">예</label>
                                <input type="radio" name="adult" id="adult_no" @if($user->adult === 0) checked @endif value="0" />
                                    <label for="adult_no">아니오</label>
                            </td>
                        </tr>
                        <tr>
                            <th>주소</th>
                            <td>
                                <input type="text" id="zip" name="zip" class="form-control" value="{{ $user->zip }}" placeholder="@lang('messages.zip')">
                                <input type="button" onclick="execDaumPostcode()" value="@lang('messages.address_search')"><br>

                                <div id="wrap" style="display:none;border:1px solid;width:500px;height:300px;margin:5px 0;position:relative">
                                    <img src="//t1.daumcdn.net/localimg/localimages/07/postcode/320/close.png"
                                        style="cursor:pointer;position:absolute;right:0px;top:-1px;z-index:1"
                                         id="btnFoldWrap" onclick="foldDaumPostcode()" alt="접기 버튼">
                                </div>
                                <input type="text" id="addr1" name="addr1" class="form-control" value="{{ $user->addr1 }}" placeholder="@lang('messages.address1')">
                                <input type="text" id="addr2" name="addr2" class="form-control" value="{{ $user->addr2 }}" placeholder="@lang('messages.address2')">
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
                                <input type="radio" name="mailing" id="mailing_yes" @if($user->mailing === 1) checked @endif value="1" />
                                    <label for="mailing_yes">예</label>
                                <input type="radio" name="mailing" id="mailing_no" @if($user->mailing === 0) checked @endif value="0" />
                                    <label for="mailing_no">아니오</label>
                            </td>
                            <th>SMS 수신</th>
                            <td>
                                <input type="radio" name="sms" id="sms_yes" @if($user->sms === 1) checked @endif value="1" />
                                    <label for="sms_yes">예</label>
                                <input type="radio" name="sms" id="sms_no" @if($user->sms === 0) checked @endif value="0" />
                                    <label for="sms_no">아니오</label>
                            </td>
                        </tr>
                        <tr>
                            <th>정보 공개</th>
                            <td>
                                <input type="radio" name="open" id="open_yes" @if($user->open === 1) checked @endif value="1" />
                                    <label for="open_yes">예</label>
                                <input type="radio" name="open" id="open_no" @if($user->open === 0) checked @endif value="0" />
                                    <label for="open_no">아니오</label>
                            </td>
                        </tr>
                        <tr>
                            <th>서명</th>
                            <td>
                                <textarea name="signature" class="form-control">{{ $user->signature }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>자기 소개</th>
                            <td>
                                <textarea name="profile" class="form-control">{{ $user->profile }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>메모</th>
                            <td>
                                <textarea name="memo" class="form-control">{{ $user->memo }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>회원가입일</th>
                            <td>@datetime($user->created_at)</td>
                            <th>최근접속일</th>
                            <td>{{ $user->today_login }}</td>
                        </tr>
                        <tr>
                            <th>IP</th>
                            <td>{{ $user->ip }}</td>
                        </tr>
                        <tr>
                            <th>탈퇴일자</th>
                            <td>
                                <input type="text" class="form-control" name="leave_date" id="leave_date"
                                    value="{{ $user->leave_date }}" />
                                <input type="checkbox" name="leave_date_set_today" value="1" id="leave_date_set_today"
                                    onclick="setToday(this.form.leave_date_set_today, this.form.leave_date)"/>
                                <label for="leave_date_set_today">탈퇴일을 오늘로 지정</label>
                            </td>
                            <th>접근차단일자</th>
                            <td>
                                <input type="text" class="form-control" name="intercept_date" id="intercept_date"
                                    value="{{ $user->intercept_date }}" />
                                <input type="checkbox" name="intercept_date_set_today" id="intercept_date_set_today" value="1"
                                    onclick="setToday(this.form.intercept_date_set_today, this.form.intercept_date)"/>
                                <label for="intercept_date_set_today">접근차단일을 오늘로 지정</label>
                            </td>
                        </tr>
                    </table>
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                {{ method_field('PUT') }}
                                변경
                            </button>
                            <a class="btn btn-primary" href="{{ route('users.index') }}">목록</a>
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
