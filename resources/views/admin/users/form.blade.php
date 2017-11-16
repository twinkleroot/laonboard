@extends('admin.layouts.basic')

@section('title')회원@if($type == 'create') 추가@else 정보 수정@endif | {{ cache("config.homepage")->title }}@endsection

@section('include_script')
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script src="{{ ver_asset('js/postcode.js') }}"></script>
<script type="text/javascript">
    var menuVal = 200100;
    $(document).ready(function($){
        $(window).on('scroll', function() {
            $('.adm_section').each(function() {
                if($(window).scrollTop() >= $(this).offset().top - 100) {
                    var id = $(this).attr('id');
                    $('#body_tab_type2 ul li').removeClass('active');
                    $("#body_tab_type2 ul li a[href='#" + id + "']").parent().addClass('active');
                } else if($(window).scrollTop() >= $('#bottom').position().top - $(window).outerHeight(true) + 100) {   // 제일 밑으로 스크롤을 내렸을 때
                    var id = 'mb_more';
                    $('#body_tab_type2 ul li').removeClass('active');
                    $("#body_tab_type2 ul li a[href='#" + id + "']").parent().addClass('active');
                }
            });
        });
    });
</script>
@endsection

@section('content')
{{ session()->put('user_leave_date', $user->leave_date) }}
{{ session()->put('user_intercept_date', $user->intercept_date) }}
@if($type == 'update')
<form role="form" method="POST" action="{{ route('admin.users.update', $id) }}" enctype="multipart/form-data" autocomplete="off">
@else
<form role="form" method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data" autocomplete="off">
@endif
{{ csrf_field() }}
<div class="body-head">
    <div class="pull-left">
        <h3>회원@if($type == 'update') 수정@else 추가@endif</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">회원 관리</li>
            <li class="depth">회원@if($type == 'update') 수정@else 추가@endif</li>
        </ul>
    </div>
</div>

<div id="body_tab_type2">
    <ul>
        <li class="tab"><a href="#mb_basic">기본정보</a></li>
        <li class="tab"><a href="#mb_add">추가정보</a></li>
        <li class="tab"><a href="#mb_and">부가설정</a></li>
        {{ fireEvent('adminUserFormTab') }}
        <li class="tab"><a href="#mb_more">여분필드</a></li>
    </ul>
    <div class="submit_btn">
        @if($type == 'update')
        <button type="submit" class="btn btn-sir">{{ method_field('PUT') }}변경</button>
        @else
        <button type="submit" class="btn btn-sir">확인</button>
        @endif
        <a href="{{ route('admin.users.index'). '?'. Request::getQueryString() }}" class="btn btn-default" role="button">목록</a>
    </div>
</div>
<div class="body-contents">
@if(Session::has('message'))
    <div id="adm_save">
        <span class="adm_save_txt">{{ Session::get('message') }}</span>
        <button onclick="alertclose()" class="adm_alert_close">
            <i class="fa fa-times"></i>
        </button>
    </div>
@endif
@if ($errors->any())
    <div id="adm_save">
        <span class="adm_save_txt">{{ $errors->first() }}</span>
        <button onclick="alertclose()" class="adm_alert_close">
            <i class="fa fa-times"></i>
        </button>
    </div>
@endif
    <section id="mb_basic" class="adm_section">
        <div class="adm_box_hd">
            <span class="adm_box_title">기본 회원정보</span>
        </div>
        <table class="adm_box_table">
            <tr>
                <th><label for="email">이메일</label></th>
                <td class="table_body @if($errors->get('email')) has-error @endif">
                    @if($type == 'update')
                    <input type="email" class="form-control form_large required" name="email" value="{{ $user->email }}" style="display: inline-block;" readonly>
                    <a href="{{ route('admin.accessGroups.show', $user->id). '?'. Request::getQueryString() }}" class="btn btn-sir form_btn">접근가능그룹보기</a>
                    @else
                    <input type="email" class="form-control form_large required" name="email" value="{{ old('email') }}" style="display: inline-block;">
                    @endif
                    @foreach ($errors->get('email') as $message)
                    <span class="help-block">
                        <strong>{{ $message }}</strong>
                    </span>
                    @endforeach
                </td>
            </tr>
            <tr>
                @if($type == 'update')
                <th><label for="change_password">비밀번호</label></th>
                @else
                <th><label for="password">비밀번호</label></th>
                @endif
                <td class="table_body @if($errors->get('password')) has-error @endif">
                    @if($type == 'update')
                    <input type="password" class="form-control form_large" name="change_password" value="">
                    @else
                    <input type="password" class="form-control form_large required" name="password" value="">
                    @endif
                    @foreach ($errors->get('password') as $message)
                    <span class="help-block">
                        <strong>{{ $message }}</strong>
                    </span>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th><label for="name">이름</label></th>
                <td class="table_body @if($errors->get('name')) has-error @endif">
                    <input type="text" class="form-control form_middle" name="name" @if($type == 'update') value="{{ $user->name }}"@else value="{{ old('name') }}"@endif>
                    @foreach ($errors->get('name') as $message)
                    <span class="help-block">
                        <strong>{{ $message }}</strong>
                    </span>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th><label for="nick">닉네임</label></th>
                <td class="table_body @if($errors->get('nick')) has-error @endif">
                    <input type="text" class="form-control form_middle required" name="nick" @if($type == 'update') value="{{ $user->nick }}"@else value="{{ old('nick') }}"@endif>
                    @foreach ($errors->get('nick') as $message)
                    <span class="help-block">
                        <strong>{{ $message }}</strong>
                    </span>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th><label for="level">회원권한</label></th>
                <td class="table_body @if($errors->get('level')) has-error @endif">
                    <select name="level" class="form-control level form_num">
                    @for ($i=1; $i<=auth()->user()->level; $i++)
                        @if($type == 'update')
                        <option value='{{ $i }}' @if($user->level == $i) selected @endif>
                            {{ $i }}
                        </option>
                        @else
                        <option value={{ $i }} @if(Cache::get("config.join")->joinLevel == $i) selected @endif>
                            {{ $i }}
                        </option>
                        @endif
                    @endfor
                    </select>
                    @foreach ($errors->get('level') as $message)
                    <span class="help-block">
                        <strong>{{ $message }}</strong>
                    </span>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>포인트</th>
                <td class="table_body @if($errors->get('point')) has-error @endif">
                @if($type == 'update')
                    @if($user->point == 0) 0 @else {{ number_format($user->point) }} @endif 점
                    <span class="help-block">
                        포인트 부여 및 차감은 <a href="{{ route('admin.points.index') }}">[회원관리 - 포인트관리]</a>에서 하실 수 있습니다.
                    </span>
                @else
                    <input type="text" class="form-control form_middle" name="point" value="{{ old('point') != Cache::get("config.join")->joinPoint ? old('point') : Cache::get("config.join")->joinPoint }}">
                @endif
                @foreach ($errors->get('point') as $message)
                    <span class="help-block">
                        <strong>{{ $message }}</strong>
                    </span>
                @endforeach
                </td>
            </tr>
            @if($type == 'update')
            <tr>
                <th>회원가입일</th>
                <td class="table_body">
                    @if($user->created_at) @date($user->created_at) @endif
                </td>
            </tr>
            <tr>
                <th>최근접속일</th>
                <td class="table_body">
                    {{ $user->today_login }}
                </td>
            </tr>
            <tr>
                <th>IP주소</th>
                <td class="table_body">
                    {{ $user->ip }}
                </td>
            </tr>
            <tr>
                <th>회원상태</th>
                <td class="table_body">
                    @if($user->leave_date)
                    <span class="mb_msg withdraw">탈퇴</span>
                    @elseif ($user->intercept_date)
                    <span class="mb_msg intercept">차단</span>
                    @else
                    <span class="mb_msg">정상</span>
                    @endif
                    <span class="help-block">하단의 탈퇴일자 혹은 접근차단일자를 지정하면 회원상태가 변경됩니다.</span>
                </td>
            </tr>
            @endif
            <tr>
                <th><label for="leave_date">탈퇴일자</label></th>
                <td class="table_body @if($errors->get('leave_date')) has-error @endif">
                    <input type="text" class="form-control form_middle" name="leave_date" id="leave_date" @if($type == 'update') value="{{ $user->leave_date }}"@else value="{{ old('leave_date') }}"@endif>
                    <input type="checkbox" name="leave_date_set_today" value="1" id="leave_date_set_today" onclick="setToday(this.form.leave_date_set_today, this.form.leave_date)"/>
                    <label for="leave_date_set_today">탈퇴일을 오늘로 지정</label>
                    @foreach ($errors->get('leave_date') as $message)
                    <span class="help-block">
                        <strong>{{ $message }}</strong>
                    </span>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th><label for="intercept_date">접근차단일자</label></th>
                <td class="table_body @if($errors->get('intercept_date')) has-error @endif">
                    <input type="text" class="form-control form_middle" name="intercept_date" id="intercept_date" @if($type == 'update') value="{{ $user->intercept_date }}"@else value="{{ old('intercept_date') }}"@endif>
                    <input type="checkbox" name="intercept_date_set_today" id="intercept_date_set_today" value="1" onclick="setToday(this.form.intercept_date_set_today, this.form.intercept_date)"/>
                    <label for="intercept_date_set_today">접근차단일을 오늘로 지정</label>
                    @foreach ($errors->get('intercept_date') as $message)
                    <span class="help-block">
                        <strong>{{ $message }}</strong>
                    </span>
                    @endforeach
                </td>
            </tr>
        </table>
    </section>
    <section id="mb_add" class="adm_section">
        <div class="adm_box_hd">
            <span class="adm_box_title">추가 회원정보</span>
        </div>
        <table class="adm_box_table">
            <tr>
                <th><label for="homepage">홈페이지</label></th>
                <td class="table_body @if($errors->get('homepage')) has-error @endif">
                    <input type="text" class="form-control form_half" name="homepage" @if($type == 'update') value="{{ $user->homepage }}"@else value="{{ old('homepage') }}"@endif>
                    @foreach ($errors->get('homepage') as $message)
                    <span class="help-block">
                        <strong>{{ $message }}</strong>
                    </span>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th><label for="tel">전화번호</label></th>
                <td class="table_body @if($errors->get('tel')) has-error @endif">
                    <input type="text" class="form-control form_half" name="tel" @if($type == 'update') value="{{ $user->tel }}"@else value="{{ old('tel') }}"@endif>
                    @foreach ($errors->get('tel') as $message)
                    <span class="help-block">
                        <strong>{{ $message }}</strong>
                    </span>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th><label for="hp">휴대폰번호</label></th>
                <td class="table_body @if($errors->get('hp')) has-error @endif">
                    <input type="text" class="form-control form_half" name="hp" @if($type == 'update') value="{{ $user->hp }}"@else value="{{ old('hp') }}"@endif>
                    @foreach ($errors->get('hp') as $message)
                    <span class="help-block">
                        <strong>{{ $message }}</strong>
                    </span>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th><label for="zip">주소</label></th>
                <td class="table_body">
                    <div class="mb10">
                        <input type="text" class="form-control form_middle" id="zip" name="zip" @if($type == 'update') value="{{ $user->zip }}"@else value="{{ old('zip') }}"@endif placeholder="우편번호">
                        <input type="button" class="btn btn-sir form_btn" onclick="execDaumPostcode()" value="주소 검색">
                    </div>
                    <!-- 우편번호검색 -->
                    <div class="form_half">
                        <div id="wrap" class="formaddr">
                            <img src="//t1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" style="cursor:pointer;position:absolute;right:0px;top:-1px;z-index:1" id="btnFoldWrap" onclick="foldDaumPostcode()" alt="접기 버튼">
                        </div>
                    </div>
                    <div class="mb10">
                        <label for="addr1" class="sr-only">기본주소</label>
                        <input type="text" class="form-control form_half" id="addr1" name="addr1" @if($type == 'update') value="{{ $user->addr1 }}"@else value="{{ old('addr1') }}"@endif placeholder="기본 주소">
                    </div>
                    <div>
                        <label for="addr2" class="sr-only">상세주소</label>
                        <input type="text" class="form-control form_half" id="addr2" name="addr2" @if($type == 'update') value="{{ $user->addr2 }}"@else value="{{ old('addr2') }}"@endif placeholder="상세 주소">
                    </div>
                </td>
            </tr>
            <tr>
                <th><label for="signature">서명</label></th>
                <td class="table_body">
                    <textarea class="form-control" rows="5" name="signature">@if($type == 'update'){{ $user->signature }}@else{{ old('signature') }}@endif</textarea>
                </td>
            </tr>
            <tr>
                <th><label for="profile">자기소개</label></th>
                <td class="table_body">
                    <textarea class="form-control" rows="5" name="profile">@if($type == 'update'){{ $user->profile }}@else{{ old('profile') }}@endif</textarea>
                </td>
            </tr>
            <tr>
                <th><label for="memo">메모</label></th>
                <td class="table_body">
                    <textarea class="form-control" rows="5" name="memo">@if($type == 'update'){{ $user->memo }}@else{{ old('memo') }}@endif</textarea>
                </td>
            </tr>
            <tr>
                <th><label for="recommend">추천인</label></th>
                <td class="table_body @if($errors->get('recommend')) has-error @endif">
                    <input type="text" class="form-control form_large" name="recommend" id="recommend" placeholder="닉네임" @if($type == 'update') value="{{ $recommend }}"@else value="{{ old('recommend') }}"@endif>
                    @foreach ($errors->get('recommend') as $message)
                    <span class="help-block">
                        <strong>{{ $message }}</strong>
                    </span>
                    @endforeach
                </td>
            </tr>
        </table>
    </section>
    <section id="mb_and" class="adm_section">
        <div class="adm_box_hd">
            <span class="adm_box_title">부가설정</span>
        </div>
        <table class="adm_box_table">
            <tr>
                <th><label for="mailing">메일 수신</label></th>
                <td class="table_body">
                    <input type="radio" name="mailing" id="mailing_yes" @if($user->mailing === 1) checked @endif value="1" />
                    <label for="mailing_yes">예</label>
                    <input type="radio" name="mailing" id="mailing_no" @if($user->mailing === 0) checked @endif value="0" />
                    <label for="mailing_no">아니오</label>
                </td>
            </tr>
            {{-- <tr>
                <th><label for="sms">SMS 수신</label></th>
                <td class="table_body">
                    <input type="radio" name="sms" id="sms_yes" @if($user->sms === 1) checked @endif value="1" />
                        <label for="sms_yes">예</label>
                    <input type="radio" name="sms" id="sms_no" @if($user->sms === 0) checked @endif value="0" />
                        <label for="sms_no">아니오</label>
                </td>
            </tr> --}}
            <tr>
                <th><label for="open">정보공개</label></th>
                <td class="table_body">
                    <input type="radio" name="open" id="open_yes" @if($user->open === 1) checked @endif value="1" />
                    <label for="open_yes">예</label>
                    <input type="radio" name="open" id="open_no" @if($user->open === 0) checked @endif value="0" />
                    <label for="open_no">아니오</label>
                </td>
            </tr>
            <tr>
                <th><label for="icon">회원아이콘</label></th>
                <td class="table_body @if($errors->get('iconName')) has-error @endif">
                    @if($type == 'update' && File::exists($iconPath))
                    <div class="usericon">
                        <span class="usericon_img">
                            <img alt="회원아이콘" src="{{ $iconUrl }}">
                        </span>
                        <span class="usericon_del">
                            <input type="checkbox" name="delIcon" value="1" id="delIcon">
                            <label for="delIcon">삭제</label>
                        </span>
                    </div>
                    @endif
                    <input type="file" name="icon" id="icon" value="{{ old('icon') }}">
                    <span class="help-block">이미지 크기는 넓이 {{ cache('config.join')->memberIconWidth }}픽셀 높이 {{ cache('config.join')->memberIconHeight }}픽셀로 해주세요.<br>
                    gif만 가능하며 용량 {{ cache('config.join')->memberIconSize }}바이트 이하만 등록됩니다.
                    </span>
                    @foreach ($errors->get('iconName') as $message)
                    <span class="help-block">
                        <strong>{{ $message }}</strong>
                    </span>
                    @endforeach
                </td>
            </tr>
        </table>
    </section>

    {{ fireEvent('adminUserForm') }}

    <section id="mb_more" class="adm_section">
        <div class="adm_box_hd">
            <span class="adm_box_title">여분필드</span>
        </div>
        <table class="adm_box_table">
            @for($i=1; $i<=10; $i++)
                <tr>
                    <th>
                        <label for="extra_{{ $i }}">여분필드{{ $i }}</label>
                    </th>
                    <td class="table_body">
                        <input type="text" class="form-control form_half"  name="extra_{{ $i }}" value="{{ $user['extra_'. $i] }}">
                    </td>
                </tr>
            @endfor
        </table>
    </section>
    <section id="bottom"></section>
</div>
</form>
<script>
function setToday(chkbox, place) {
    var now = new Date();
    var storedDate = '';
    if(place.name.indexOf('leave') == 0) {
        storedDate = '{{ session()->get('user_leave_date') }}';
    } else if(place.name.indexOf('intercept') == 0) {
        storedDate = '{{ session()->get('user_intercept_date') }}';
    }
    if(chkbox.checked) {
        $(place).val(getFormattedDate(now));
    } else {
        $(place).val(storedDate);
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
