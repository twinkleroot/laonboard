@extends('admin.admin')

@section('title')
    회원 정보 수정 | {{ cache("config.homepage")->title }}
@endsection

@section('include_script')
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script src="{{ url('js/postcode.js') }}"></script>
<script type="text/javascript">
    var menuVal = 100100;
    jQuery("document").ready(function($){
        var nav = $('#body_tab_type2');
        $(window).scroll(function () {
            if ($(this).scrollTop() > 175) {
                nav.addClass("f-tab");
            } else {
                nav.removeClass("f-tab");
            }
        });
        $(window).on('scroll', function() {
            $('.adm_section').each(function() {
                if($(window).scrollTop() >= $(this).offset().top - 100) {
                    var id = $(this).attr('id');
                    $('#body_tab_type2 ul li').removeClass('active');
                    $("#body_tab_type2 ul li a[href='#" + id + "']").parent().addClass('active');
                } else if($(window).scrollTop() >= $('#bottom').position().top - $(window).outerHeight(true) + 100) {   // 제일 밑으로 스크롤을 내렸을 때
                    var id = 'more';
                    $('#body_tab_type2 ul li').removeClass('active');
                    $("#body_tab_type2 ul li a[href='#" + id + "']").parent().addClass('active');
                }
            });
        });
    });
</script>
@endsection

@section('content')
<form role="form" method="POST" action="{{ route('admin.users.update', $id) }}">
{{ csrf_field() }}
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
<div id="body_tab_type2">
    <ul>
        <li class="tab"><a href="#mb_basic">기본정보</a></li>
        <li class="tab"><a href="#mb_add">추가정보</a></li>
        <li class="tab"><a href="#mb_and">부가설정</a></li>
        <li class="tab"><a href="#mb_cert">본인인증</a></li>
        <li class="tab"><a href="#mb_more">여분필드</a></li>
    </ul>

    <div class="submit_btn">
        <button type="submit" class="btn btn-sir">{{ method_field('PUT') }}변경</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-default" role="button">목록</a>
    </div>
</div>
<div class="body-contents">
    <section id="mb_basic" class="adm_section">
        <div class="adm_box_hd">
            <span class="adm_box_title">기본 회원정보</span>
        </div>
        <table class="adm_box_table">
            <tr>
                <th><label for="email">이메일</label></th>
                <td class="table_body chknone">
                    <input type="email" class="form-control form_large required" name="email" value="{{ $user->email }}" style="display: inline-block;" readonly>
                    <a href="{{ route('admin.accessGroups.show', $user->id). '?'. Request::getQueryString() }}" class="btn btn-sir form_btn">접근가능그룹보기</a>
                </td>
            </tr>
            <tr>
                <th><label for="change_password">비밀번호</label></th>
                <td class="table_body chknone @if($errors->get('password')) has-error @endif">
                    <input type="password" class="form-control form_large" name="change_password" value="">
                    @foreach ($errors->get('password') as $message)
                        <span class="help-block">
                            <strong>{{ $message }}</strong>
                        </span>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th><label for="name">이름</label></th>
                <td class="table_body chknone  @if($errors->get('name')) has-error @endif">
                    <input type="text" class="form-control form_middle"  name="name" value="{{ $user->name }}">
                    @foreach ($errors->get('name') as $message)
                        <span class="help-block">
                            <strong>{{ $message }}</strong>
                        </span>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th><label for="nick">닉네임</label></th>
                <td class="table_body chknone @if($errors->get('nick')) has-error @endif">
                    <input type="text" class="form-control form_middle required" name="nick" value="{{ $user->nick }}">
                    @foreach ($errors->get('nick') as $message)
                        <span class="help-block">
                            <strong>{{ $message }}</strong>
                        </span>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th><label for="level">회원권한</label></th>
                <td class="table_body chknone">
                    <select name="level" class="form-control level form_num">
                    @for ($i=1; $i<=auth()->user()->level; $i++)
                        <option value='{{ $i }}' @if($user->level == $i) selected @endif>
                            {{ $i }}
                        </option>
                    @endfor
                </td>
            </tr>
            <tr>
                <th>포인트</th>
                <td class="table_body chknone">
                    @if($user->point == 0) 0 @else {{ number_format($user->point) }} @endif 점
                    <span class="help-block">
                        포인트 부여 및 차감은 <a href="{{ route('admin.points.index') }}">[회원관리 - 포인트관리]</a>에서 하실 수 있습니다.
                    </span>
                </td>
            </tr>
            <tr>
                <th>회원가입일</th>
                <td class="table_body chknone">
                    @datetime($user->created_at)
                </td>
            </tr>
            <tr>
                <th>최근접속일</th>
                <td class="table_body chknone">
                    {{ $user->today_login }}
                </td>
            </tr>
            <tr>
                <th>IP주소</th>
                <td class="table_body chknone">
                    {{ $user->ip }}
                </td>
            </tr>
            <tr>
                <th>회원상태</th>
                <td class="table_body chknone">
                    @if(!is_null($user->leave_date))
                        <span class="mb_msg withdraw">탈퇴</span>
                    @elseif (!is_null($user->intercept_date))
                        <span class="mb_msg intercept">차단</span>
                    @else
                        <span class="mb_msg">정상</span>
                    @endif
                    <span class="help-block">하단의 탈퇴일자 혹은 접근차단일자를 지정하면 회원상태가 변경됩니다.</span>
                </td>
            </tr>
            <tr>
                <th><label for="leave_date">탈퇴일자</label></th>
                <td class="table_body chknone">
                    <input type="text" class="form-control form_middle" name="leave_date" id="leave_date" value="{{ $user->leave_date }}">
                    <input type="checkbox" name="leave_date_set_today" value="1" id="leave_date_set_today" onclick="setToday(this.form.leave_date_set_today, this.form.leave_date)"/>
                    <label for="leave_date_set_today">탈퇴일을 오늘로 지정</label>
                </td>
                <td></td>
            </tr>
            <tr>
                <th><label for="intercept_date">접근차단일자</label></th>
                <td class="table_body chknone">
                    <input type="text" class="form-control form_middle" name="intercept_date" id="intercept_date" value="{{ $user->intercept_date }}">
                    <input type="checkbox" name="intercept_date_set_today" id="intercept_date_set_today" value="1" onclick="setToday(this.form.intercept_date_set_today, this.form.intercept_date)"/>
                    <label for="intercept_date_set_today">접근차단일을 오늘로 지정</label>
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
                <td class="table_body chknone">
                    <input type="text" class="form-control form_half" name="homepage" value="{{ $user->homepage }}">
                </td>
            </tr>
            <tr>
                <th><label for="tel">전화번호</label></th>
                <td class="table_body chknone @if($errors->get('tel')) has-error @endif">
                    <input type="text" class="form-control form_half" name="tel" value="{{ $user->tel }}">
                    @foreach ($errors->get('tel') as $message)
                        <span class="help-block">
                            <strong>{{ $message }}</strong>
                        </span>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th><label for="hp">휴대폰번호</label></th>
                <td class="table_body chknone @if($errors->get('hp')) has-error @endif">
                    <input type="text" class="form-control form_half" name="hp" value="{{ $user->hp }}">
                    @foreach ($errors->get('hp') as $message)
                        <span class="help-block">
                            <strong>{{ $message }}</strong>
                        </span>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th><label for="zip">주소</label></th>
                <td class="table_body chknone">
                    <div class="mb10">
                        <input type="text" class="form-control form_middle" id="zip" name="zip" value="{{ $user->zip }}" placeholder="우편번호">
                        <input type="button" class="btn btn-sir form_btn" onclick="execDaumPostcode()" value="주소검색">
                    </div>
                    <!-- 우편번호검색 -->
                    <div class="form_half">
                        <div id="wrap" class="formaddr">
                            <img src="//t1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" style="cursor:pointer;position:absolute;right:0px;top:-1px;z-index:1" id="btnFoldWrap" onclick="foldDaumPostcode()" alt="접기 버튼">
                        </div>
                    </div>
                    <div class="mb10">
                        <label for="addr1" class="sr-only">기본주소</label>
                        <input type="text" class="form-control form_half" id="addr1" name="addr1" value="{{ $user->addr1 }}" placeholder="기본 주소">
                    </div>
                    <div>
                        <label for="addr2" class="sr-only">상세주소</label>
                        <input type="text" class="form-control form_half" id="addr2" name="addr2" value="{{ $user->addr2 }}" placeholder="상세 주소">
                    </div>
                </td>
            </tr>
            <tr>
                <th><label for="signature">서명</label></th>
                <td class="table_body chknone">
                    <textarea class="form-control" rows="5" name="signature">{{ $user->signature }}</textarea>
                </td>
            </tr>
            <tr>
                <th><label for="profile">자기소개</label></th>
                <td class="table_body chknone">
                    <textarea class="form-control" rows="5" name="profile">{{ $user->profile }}</textarea>
                </td>
            </tr>
            <tr>
                <th><label for="memo">메모</label></th>
                <td class="table_body chknone">
                    <textarea class="form-control" rows="5" name="memo">{{ $user->memo }}</textarea>
                </td>
            </tr>
            <tr>
                <th><label for="?">추천인</label></th>
                <td class="table_body chknone">
                    <input type="text" class="form-control form_large" id="?">
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
                <td class="table_body chknone">
                    <input type="radio" name="mailing" id="mailing_yes" @if($user->mailing === 1) checked @endif value="1" />
                        <label for="mailing_yes">예</label>
                    <input type="radio" name="mailing" id="mailing_no" @if($user->mailing === 0) checked @endif value="0" />
                        <label for="mailing_no">아니오</label>
                </td>
            </tr>
            <tr>
                <th><label for="sms">SMS 수신</label></th>
                <td class="table_body chknone">
                    <input type="radio" name="sms" id="sms_yes" @if($user->sms === 1) checked @endif value="1" />
                        <label for="sms_yes">예</label>
                    <input type="radio" name="sms" id="sms_no" @if($user->sms === 0) checked @endif value="0" />
                        <label for="sms_no">아니오</label>
                </td>
            </tr>
            <tr>
                <th><label for="open">정보공개</label></th>
                <td class="table_body chknone">
                    <input type="radio" name="open" id="open_yes" @if($user->open === 1) checked @endif value="1" />
                        <label for="open_yes">예</label>
                    <input type="radio" name="open" id="open_no" @if($user->open === 0) checked @endif value="0" />
                        <label for="open_no">아니오</label>
                </td>
            </tr>
            <tr>
                <th><label for="icon">회원아이콘</label></th>
                <td class="table_body chknone">
                    <input type="file" name="icon">
                    <span class="help-block">이미지 크기는 넓이 22픽셀 높이 22픽셀로 해주세요.</span>
                </td>
            </tr>
        </table>
    </section>
    <section id="mb_cert" class="adm_section">
        <div class="adm_box_hd">
            <span class="adm_box_title">본인인증</span>
        </div>
        <table class="adm_box_table">
            <tr>
                <th><label for="certify_case">본인확인방법</label></th>
                <td class="table_body chknone">
                    <input type="radio" name="certify_case" id="certify_case_ipin" value="0" @if($user->certify == 'ipin') checked @endif />
                        <label for="certify_case_ipin">아이핀</label>
                    <input type="radio" name="certify_case" id="certify_case_hp" value="1" @if($user->certify == 'hp') checked @endif />
                        <label for="certify_case_hp">휴대폰</label>
                </td>
            </tr>
            <tr>
                <th><label for="certify_case">본인확인방법</label></th>
                <td class="table_body chknone">
                    <input type="radio" name="certify_case" id="certify_case_ipin" value="0" @if($user->certify == 'ipin') checked @endif />
                        <label for="certify_case_ipin">아이핀</label>
                    <input type="radio" name="certify_case" id="certify_case_hp" value="1" @if($user->certify == 'hp') checked @endif />
                        <label for="certify_case_hp">휴대폰</label>
                </td>
            </tr>
            <tr>
                <th><label for="certify">본인확인</th>
                <td class="table_body chknone">
                    <input type="radio" name="certify" id="certify_yes" @if($user->certify) checked @endif value="1" />
                        <label for="certify_yes">예</label>
                    <input type="radio" name="certify" id="certify_no" @if(!$user->certify || empty($user->certify)) checked @endif value="0" />
                        <label for="certify_no">아니오</label>
                </td>
            </tr>
            <tr>
                <th><label for="adult">성인인증</th>
                <td class="table_body chknone">
                    <input type="radio" name="adult" id="adult_yes" @if($user->adult) checked @endif value="1" />
                        <label for="adult_yes">예</label>
                    <input type="radio" name="adult" id="adult_no" @if(!$user->adult || empty($user->adult)) checked @endif value="0" />
                        <label for="adult_no">아니오</label>
                </td>
            </tr>
        </table>
    </section>
    <section id="mb_more" class="adm_section">
        <div class="adm_box_hd">
            <span class="adm_box_title">여분필드</span>
        </div>
        <table class="adm_box_table">
            @for($i=1; $i<11; $i++)
                <tr>
                    <th>
                        <label for="extra_{{ $i }}">여분필드{{ $i }}</label>
                    </th>
                    <td class="table_body chknone">
                        <input type="text" class="form-control form_half"  name="extra_{{ $i }}" value="{{ $user['extra_'. $i] }}">
                    </td>
                </tr>
            @endfor
        </table>
    </section>
</div>
</form>
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
