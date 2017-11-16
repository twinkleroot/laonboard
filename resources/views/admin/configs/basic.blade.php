@extends('admin.layouts.basic')

@section('title')환경 설정 | {{ $configHomepage->title }}@endsection

@section('include_script')
<script type="text/javascript">
    var menuVal = 100100;
    $(document).ready(function(){
        $(window).on('scroll', function() {
            $('.adm_box').each(function() {
                if($(window).scrollTop() >= $(this).offset().top - 100) {
                    var id = $(this).attr('id');
                    $('#body_tab_type2 ul li').removeClass('active');
                    $("#body_tab_type2 ul li a[href='#" + id + "']").parent().addClass('active');
                } else if($(window).scrollTop() >= $('#bottom').position().top - $(window).outerHeight(true)) {	// 제일 밑으로 스크롤을 내렸을 때
                    var id = 'cfs_extra';
                    $('#body_tab_type2 ul li').removeClass('active');
                    $("#body_tab_type2 ul li a[href='#" + id + "']").parent().addClass('active');
                }
            });
        });

    });

    function formSubmit() {
        $("#configForm").submit();
    };
</script>
@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>기본 환경 설정</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">환경 설정</li>
            <li class="depth">기본 환경 설정</li>
        </ul>
    </div>
</div>

<div id="body_tab_type2">
    <ul>
        <li class="tab"><a href="#cfs_basic">기본환경설정</a></li>
        <li class="tab"><a href="#cfs_board">게시판기본</a></li>
        <li class="tab"><a href="#cfs_join">회원가입</a></li>
        <li class="tab"><a href="#cfs_mail">메일환경</a></li>
        <li class="tab"><a href="#cfs_mail_board">글작성시 메일</a></li>
        <li class="tab"><a href="#cfs_mail_join">회원가입시 메일</a></li>
        <li class="tab"><a href="#cfs_sns">소셜로그인</a></li>
        <li class="tab"><a href="#cfs_extra">여분필드</a></li>
    </ul>

    <div class="submit_btn">
        <button type="button" class="btn btn-default" onclick="formSubmit();">설정 변경</button>
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

<form role="form" method="POST" id="configForm" action="{{ route('admin.config.update') }}">
    {{ method_field('PUT') }}
    {{ csrf_field() }}
    <section id="cfs_basic" class="adm_box first">
        <div class="adm_box_hd">
            <span class="adm_box_title">기본 환경설정</span>
        </div>
        <table class="adm_box_table">
            <tr>
                <th>홈페이지 제목</th>
                <td class="table_body">
                    <input type="text" class="form-control form_large required" name="title" value="{{ $configHomepage->title }}" required>
                </td>
            </tr>
            <tr>
                <th>최고관리자</th>
                <td class="table_body">
                    <select class="form-control form_large" name="superAdmin">
                        <option value='' @if($configHomepage->superAdmin == '') selected @endif>
                            선택안함
                        </option>
                        @foreach($admins as $admin)
                            <option value='{{ $admin->email }}' @if($configHomepage->superAdmin == $admin->email) selected @endif>
                                {{ $admin->email }}
                            </option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>포인트 사용</th>
                <td class="table_body">
                    <input type="checkbox" name="usePoint" id="usePoint" value="1" @if($configHomepage->usePoint == 1) checked @endif>
                    <label for="usePoint">사용</label>
                </td>
            </tr>
            <tr>
                <th>로그인시 포인트</th>
                <td class="table_body">
                    <input type="text" class="form-control form_small required" name="loginPoint" value="{{ $configHomepage->loginPoint }}">
                    <span class="help-block">회원이 로그인시 하루에 한번만 적립</span>
                </td>
            </tr>
            <tr>
                <th>쪽지보낼시 차감 포인트</th>
                <td class="table_body">
                    <input type="text" class="form-control form_small required" name="memoSendPoint" value="{{ $configHomepage->memoSendPoint }}">
                    <span class="help-block">양수로 입력하십시오. 0점은 쪽지 보낼시 포인트를 차감하지 않습니다.</span>
                </td>
            </tr>
            <tr>
                <th>정보공개 수정</th>
                <td class="table_body">
                    수정하면 <input type="text" name="openDate" class="form-control form_num" value="{{ $configHomepage->openDate }}">일 동안 바꿀 수 없음
                </td>
            </tr>
            <tr>
                <th>최근게시물 삭제</th>
                <td class="table_body">
                    <input type="text" name="newDel" class="form-control form_num" value="{{ $configHomepage->newDel }}">
                    <span class="help-block">설정일이 지난 최근게시물 자동 삭제</span>
                </td>
            </tr>
            <tr>
                <th>쪽지 삭제</th>
                <td class="table_body">
                    <input type="text" name="memoDel" class="form-control form_num" value="{{ $configHomepage->memoDel }}">
                    <span class="help-block">설정일이 지난 쪽지 자동 삭제</span>
                </td>
            </tr>
            <tr>
                <th>새글 라인수</th>
                <td class="table_body">
                    <input type="text" name="newRows" class="form-control form_num" value="{{ $configHomepage->newRows }}">라인
                    <span class="help-block">목록 한 페이지당 라인수</span>
                </td>
            </tr>
            <tr>
                <th>한 페이지당 라인수</th>
                <td class="table_body">
                    <input type="text" name="pageRows" class="form-control form_num" value="{{ $configHomepage->pageRows }}">라인
                    <span class="help-block">목록(리스트) 한 페이지당 라인수</span>
                </td>
            </tr>
            {{-- <tr>
                <th>모바일 한 페이지당 라인수</th>
                <td class="table_body">
                    <input type="text" name="mobilePageRows" class="form-control form_num" value="{{ $configHomepage->mobilePageRows }}">라인
                    <span class="help-block">목록 한 페이지당 라인수</span>
                </td>
            </tr> --}}
            {{-- <tr>
                <th>페이지 표시 수</th>
                <td class="table_body">
                    <input type="text" name="writePages" class="form-control form_num required" value="{{ $configHomepage->writePages }}">페이지씩 표시
                </td>
            </tr> --}}
            {{-- <tr>
                <th>모바일 페이지 표시 수</th>
                <td class="table_body">
                    <input type="text" name="mobilePages" class="form-control form_num required" value="{{ $configHomepage->mobilePages }}">페이지씩 표시
                </td>
            </tr> --}}
            <tr>
                <th>새글 스킨</th>
                <td class="table_body">
                    <select name="newSkin" class="form-control form_middle">
                        @foreach($newSkins as $key => $value)
                            <option value='{{ $key }}' @if($configHomepage->newSkin == $key) selected @endif>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>검색 스킨</th>
                <td class="table_body">
                    <select name='searchSkin' class="form-control form_middle">
                        @foreach($searchSkins as $key => $value)
                            <option value='{{ $key }}' @if($configHomepage->searchSkin == $key) selected @endif>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>복사, 이동시 로그</th>
                <td class="table_body">
                    <input type="checkbox" name="useCopyLog" id="useCopyLog" value="1" @if($configHomepage->useCopyLog == 1) checked @endif>
                    <label for="useCopyLog">남김</label>
                    <span class="help-block">게시물 아래에 누구로 부터 복사, 이동됨 표시</span>
                </td>
            </tr>
            <tr>
                <th>포인트 유효기간</th>
                <td class="table_body">
                    <input type="text" name="pointTerm" class="form-control form_num required" value="{{ $configHomepage->pointTerm }}">일
                    <span class="help-block">기간을 0으로 설정시 포인트 유효기간이 적용되지 않습니다.</span>
                </td>
            </tr>
            <tr>
                <th>방문자분석 스크립트</th>
                <td class="table_body">
                    <textarea cols="80" rows="10" name="analytics" class="form-control">{{ $configHomepage->analytics }}</textarea>
                    <span class="help-block">방문자분석 스크립트 코드를 입력합니다. 예) 구글 애널리틱스</span>
                </td>
            </tr>
            <tr>
                <th>추가 메타태그</th>
                <td class="table_body">
                    <textarea cols="80" rows="10" name="addMeta" class="form-control">{{ $configHomepage->addMeta }}</textarea>
                    <span class="help-block">추가로 사용하실 meta 태그를 입력합니다.</span>
                </td>
            </tr>
        </table>
    </section>

    <section id="cfs_board" class="adm_box">
        <div class="adm_box_hd">
            <span class="adm_box_title">게시판 기본 설정</span>
        </div>
        <table class="adm_box_table">
            <tr>
                <td class="table_body border_none" colspan="2">
                    각 게시판 관리에서 개별적으로 설정 가능합니다.
                </td>
            </tr>
            <tr>
                <th>
                    <label for="linkTarget">새창 링크</label>
                </th>
                <td class="table_body">
                    <select name="linkTarget" class="form-control form_small">
                        <option value="_blank" @if($configBoard->linkTarget == '_blank') selected @endif>_blank</option>
                        <option value="_self" @if($configBoard->linkTarget == '_self') selected @endif>_self</option>
                        <option value="_top" @if($configBoard->linkTarget == '_top') selected @endif>_top</option>
                        <option value="_new" @if($configBoard->linkTarget == '_new') selected @endif>_new</option>
                    </select>
                    <span class="help-block">글내용중 자동 링크되는 타켓을 지정합니다.</span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="readPoint">글읽기 포인트</label>
                </th>
                <td class="table_body">
                    <input type="text" name="readPoint" class="form-control form_num required" value="{{ $configBoard->readPoint }}">점
                </td>
            </tr>
            <tr>
                <th>
                    <label for="writePoint">글쓰기 포인트</label>
                </th>
                <td class="table_body">
                    <input type="text" name="writePoint" class="form-control form_num required" value="{{ $configBoard->writePoint }}">점
                </td>
            </tr>
            <tr>
                <th>
                    <label for="commentPoint">댓글쓰기 포인트</label>
                </th>
                <td class="table_body">
                    <input type="text" name="commentPoint" class="form-control form_num required" value="{{ $configBoard->commentPoint }}">점
                </td>
            </tr>
            <tr>
                <th>
                    <label for="downloadPoint">다운로드 포인트</label>
                </th>
                <td class="table_body">
                    <input type="text" name="downloadPoint" class="form-control form_num required" value="{{ $configBoard->downloadPoint }}">점
                </td>
            </tr>
            {{-- <tr>
                <th>
                    <label for="searchPart">검색 단위</label>
                </th>
                <td class="table_body">
                    <input type="text" name="searchPart" class="form-control form_small" value="{{ $configBoard->searchPart }}"> 건 단위로 검색
                </td>
            </tr> --}}
            <tr>
                <th>
                    <label for="imageExtension">이미지 업로드 확장자</label>
                </th>
                <td class="table_body">
                    <input type="text" name="imageExtension" class="form-control form_w90" value="{{ $configBoard->imageExtension }}">
                    <span class="help-block">게시판 글작성시 이미지 파일 업로드 가능 확장자. | 로 구분</span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="flashExtension">플래쉬 업로드 확장자</label>
                </th>
                <td class="table_body">
                    <input type="text" name="flashExtension" class="form-control form_w90" value="{{ $configBoard->flashExtension }}">
                    <span class="help-block">게시판 글작성시 플래쉬 파일 업로드 가능 확장자. | 로 구분</span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="movieExtension">동영상 업로드 확장자</label>
                </th>
                <td class="table_body">
                    <input type="text" name="movieExtension" class="form-control form_w90" value="{{ $configBoard->movieExtension }}">
                    <span class="help-block">게시판 글작성시 동영상 파일 업로드 가능 확장자. | 로 구분</span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="movieExtension">단어 필터링</label>
                </th>
                <td class="table_body">
                    <textarea cols="80" rows="10" name="filter" class="form-control">{{ $configBoard->filter[0] }}</textarea>
                    <span class="help-block">입력된 단어가 포함된 내용은 게시할 수 없습니다. 단어와 단어 사이는 ,로 구분합니다.</span>
                </td>
            </tr>
        </table>
    </section>

    <section id="cfs_join" class="adm_box">
        <div class="adm_box_hd">
            <span class="adm_box_title">회원가입</span>
        </div>
        <table class="adm_box_table">
            <tr>
                <td class="table_body" colspan="2">
                    회원가입 시 사용할 스킨과 입력 받을 정보 등을 설정할 수 있습니다.
                </td>
            </tr>
            <tr>
                <th>
                    <label for="newSkin">회원 스킨</label>
                </th>
                <td class="table_body">
                    <select name='userSkin' class="form-control form_middle">
                        @foreach($userSkins as $key => $value)
                            <option value='{{ $key }}' @if($configJoin->skin == $key) selected @endif>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="nickDate">닉네임 수정</label>
                </th>
                <td class="table_body">
                    수정하면 <input type="text" name="nickDate" class="form-control form_num" value="{{ $configJoin->nickDate }}">일 동안 바꿀 수 없음
                </td>
            </tr>
            <tr>
                <th>
                    <label for="name">이름 입력</label>
                </th>
                <td class="table_body">
                    <input type="radio" name="name" id="name_check" value="1" @if($configJoin->name == 1) checked @endif>
                        <label for="name_check">사용</label>
                    <input type="radio" name="name" id="name_uncheck" value="0" @if($configJoin->name == 0) checked @endif>
                        <label for="name_uncheck">사용하지 않음</label>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="homepage">홈페이지 입력</label>
                </th>
                <td class="table_body">
                    <input type="radio" name="homepage" id="homepage_check" value="1" @if($configJoin->homepage == 1) checked @endif>
                        <label for="homepage_check">사용</label>
                    <input type="radio" name="homepage" id="homepage_uncheck" value="0" @if($configJoin->homepage == 0) checked @endif>
                        <label for="homepage_uncheck">사용하지 않음</label>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="tel">전화번호 입력</label>
                </th>
                <td class="table_body">
                    <input type="radio" name="tel" id="tel_check" value="1" @if($configJoin->tel == 1) checked @endif>
                        <label for="tel_check">사용</label>
                    <input type="radio" name="tel" id="tel_uncheck" value="0" @if($configJoin->tel == 0) checked @endif>
                        <label for="tel_uncheck">사용하지 않음</label>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="hp">휴대폰번호 입력</label>
                </th>
                <td class="table_body">
                    <input type="radio" name="hp" id="hp_check" value="1" @if($configJoin->hp == 1) checked @endif>
                        <label for="hp_check">사용</label>
                    <input type="radio" name="hp" id="hp_uncheck" value="0" @if($configJoin->hp == 0) checked @endif>
                        <label for="hp_uncheck">사용하지 않음</label>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="addr">주소 입력</label>
                </th>
                <td class="table_body">
                    <input type="radio" name="addr" id="addr_check" value="1" @if($configJoin->addr == 1) checked @endif>
                        <label for="addr_check">사용</label>
                    <input type="radio" name="addr" id="addr_uncheck" value="0" @if($configJoin->addr == 0) checked @endif>
                        <label for="addr_uncheck">사용하지 않음</label>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="signature">서명 입력</label>
                </th>
                <td class="table_body">
                    <input type="radio" name="signature" id="signature_check" value="1" @if($configJoin->signature == 1) checked @endif>
                        <label for="signature_check">사용</label>
                    <input type="radio" name="signature" id="signature_uncheck" value="0" @if($configJoin->signature == 0) checked @endif>
                        <label for="signature_uncheck">사용하지 않음</label>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="profile">자기소개 입력</label>
                </th>
                <td class="table_body">
                    <input type="radio" name="profile" id="profile_check" value="1" @if($configJoin->profile == 1) checked @endif>
                        <label for="profile_check">사용</label>
                    <input type="radio" name="profile" id="profile_uncheck" value="0" @if($configJoin->profile == 0) checked @endif>
                        <label for="profile_uncheck">사용하지 않음</label>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="joinLevel">회원가입시 권한</label>
                </th>
                <td class="table_body">
                    <select name="joinLevel" class="level form-control form_num">
                        @for ($i=1; $i<=9; $i++)
                            <option value='{{ $i }}' @if($configJoin->joinLevel == $i) selected @endif>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="joinPoint">회원가입시 지급 포인트</label>
                </th>
                <td class="table_body">
                    <input type="text" name="joinPoint" class="form-control form_small" value="{{ $configJoin->joinPoint }}">
                </td>
            </tr>
            <tr>
                <th>
                    <label for="leaveDay">회원탈퇴후 삭제일</label>
                </th>
                <td class="table_body">
                    <input type="text" name="leaveDay" class="form-control form_num" value="{{ $configJoin->leaveDay }}">일 후 자동 삭제
                </td>
            </tr>
            <tr>
                <th>
                    <label for="useMemberIcon">회원아이콘 사용</label>
                </th>
                <td class="table_body">
                    <select name='useMemberIcon' class="level form-control form_middle">
                        <option value='0' @if($configJoin->useMemberIcon == 0) selected @endif>미사용</option>
                        <option value='1' @if($configJoin->useMemberIcon == 1) selected @endif>아이콘만 표시</option>
                        <option value='2' @if($configJoin->useMemberIcon == 2) selected @endif>아이콘+닉네임 표시</option>
                    </select>
                    <span class="help-block">게시물에 게시자 닉네임 대신 아이콘 사용</span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="iconLevel">아이콘 업로드 권한</label>
                </th>
                <td class="table_body">
                    <select name="iconLevel" class="level form-control form_num">
                        @for ($i=1; $i<=9; $i++)
                            <option value='{{ $i }}' @if($configJoin->iconLevel == $i) selected @endif>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="memberIconSize">회원아이콘 용량</label>
                </th>
                <td class="table_body">
                    <input type="text" name="memberIconSize" class="form-control form_small" value="{{ $configJoin->memberIconSize }}">바이트 이하
                </td>
            </tr>
            <tr>
                <th>
                    <label for="memberIconSize">회원아이콘 사이즈</label>
                </th>
                <td class="table_body">
                    가로 <input type="text" name="memberIconWidth" class="form-control form_num" value="{{ $configJoin->memberIconWidth }}">
                    세로 <input type="text" name="memberIconHeight" class="form-control form_num" value="{{ $configJoin->memberIconHeight }}">픽셀 이하
                </td>
            </tr>
            <tr>
                <th>
                    <label for="recommend">추천인 제도</label>
                </th>
                <td class="table_body">
                    <input type="radio" name="recommend" id="recommend_check" value="1" @if($configJoin->recommend == 1) checked @endif>
                        <label for="recommend_check">사용</label>
                    <input type="radio" name="recommend" id="recommend_uncheck" value="0" @if($configJoin->recommend == 0) checked @endif>
                        <label for="recommend_uncheck">사용하지 않음</label>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="recommend">추천인 지급 포인트</label>
                </th>
                <td class="table_body">
                    <input type="text" name="recommendPoint" class="form-control form_small" value="{{ $configJoin->recommendPoint }}">
                </td>
            </tr>
            <tr>
                <th>
                    <label for="banId">닉네임 금지단어</label>
                </th>
                <td class="table_body">
                    <textarea cols="80" rows="5" name="banId" class="form-control">{{ $configJoin->banId[0] }}</textarea>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="stipulation">회원가입약관</label>
                </th>
                <td class="table_body">
                    <textarea cols="80" rows="5" name="stipulation" class="form-control">{{ $configJoin->stipulation }}</textarea>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="stipulation">개인정보처리방침</label>
                </th>
                <td class="table_body">
                    <textarea cols="80" rows="5" name="privacy" class="form-control">{{ $configJoin->privacy }}</textarea>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="privacy">비밀번호 조합 정책</label>
                </th>
                <td class="table_body">
                    최소<input type="text" id="digits" name="passwordPolicyDigits" class="form-control form_num" value="{{ $configJoin->passwordPolicyDigits }}" placeholder="비밀 번호 최소 자릿수를 입력해 주세요." /> 자릿수 이상
                    <p>
                    <input type="checkbox" id="special" name="passwordPolicySpecial" value="1" @if($configJoin->passwordPolicySpecial == 1) checked @endif/>
                        <label for="special">특수문자 하나 이상</label>
                    <input type="checkbox" id="upper" name="passwordPolicyUpper" value="1" @if($configJoin->passwordPolicyUpper == 1) checked @endif/>
                        <label for="upper">대문자 하나 이상</label>
                    <input type="checkbox" id="number" name="passwordPolicyNumber" value="1" @if($configJoin->passwordPolicyNumber == 1) checked @endif/>
                        <label for="number">숫자 하나 이상</label>
                    </p>
                </td>
            </tr>
        </table>
    </section>

    <section id="cfs_mail" class="adm_box">
        <div class="adm_box_hd">
            <span class="adm_box_title">기본 메일 환경 설정</span>
        </div>
        <table class="adm_box_table">
            <tr>
                <th>
                    <label for="emailUse">메일발송 사용</label>
                </th>
                <td class="table_body">
                    <input type="checkbox" name="emailUse" id="emailUse" value="1" @if($configEmailDefault->emailUse == 1) checked @endif>
                    <label for="emailUse">사용</label>
                    <span class="help-block">체크하지 않으면 메일발송을 아예 사용하지 않습니다. 메일 테스트도 불가합니다.</span>
                </td>
            </tr>
            <tr>
                <th>관리자 메일 주소</th>
                <td class="table_body">
                    <input type="text" class="form-control form_large" name="adminEmail" value="{{ $configEmailDefault->adminEmail }}">
                    <span class="help-block">관리자가 보내고 받는 용도로 사용하는 메일 주소를 입력합니다. (회원가입, 인증메일, 테스트, 회원메일발송 등에서 사용)</span>
                </td>
            </tr>
            <tr>
                <th>관리자 메일 발송이름</th>
                <td class="table_body">
                    <input type="text" class="form-control form_large" name="adminEmailName" value="{{ $configEmailDefault->adminEmailName }}">
                    <span class="help-block">관리자가 보내고 받는 용도로 사용하는 메일의 발송이름을 입력합니다. (회원가입, 인증메일, 테스트, 회원메일발송 등에서 사용)</span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="emailCertify">이메일 인증 사용</label>
                </th>
                <td class="table_body">
                    <input type="checkbox" name="emailCertify" id="emailCertify" value="1" @if($configEmailDefault->emailCertify == 1) checked @endif>
                    <label for="emailCertify">사용</label>
                    <span class="help-block">메일에 배달된 인증 주소를 클릭하여야 회원으로 인정합니다.</span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="formmailIsMember">폼메일 사용 여부</label>
                </th>
                <td class="table_body">
                    <input type="checkbox" name="formmailIsMember" id="formmailIsMember" value="1" @if($configEmailDefault->formmailIsMember == 1) checked @endif>
                    <label for="formmailIsMember">회원만 사용</label>
                    <span class="help-block">체크하지 않으면 비회원도 사용 할 수 있습니다.</span>
                </td>
            </tr>
        </table>
    </section>

    <section id="cfs_mail_board" class="adm_box">
        <div class="adm_box_hd">
            <span class="adm_box_title">게시판 글 작성시 메일 설정</span>
        </div>
        <table class="adm_box_table">
            <tr>
                <th>
                    <label for="emailWriteSuperAdmin">최고관리자</label>
                </th>
                <td class="table_body">
                    <input type="checkbox" name="emailWriteSuperAdmin" id="emailWriteSuperAdmin" value="1" @if($configEmailBoard->emailWriteSuperAdmin == 1) checked @endif>
                        <label for="emailWriteSuperAdmin">사용</label>
                    <span class="help-block">최고관리자에게 메일을 발송합니다.</span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="emailWriteGroupAdmin">그룹관리자</label>
                </th>
                <td class="table_body">
                    <input type="checkbox" name="emailWriteGroupAdmin" id="emailWriteGroupAdmin" value="1" @if($configEmailBoard->emailWriteGroupAdmin == 1) checked @endif>
                        <label for="emailWriteGroupAdmin">사용</label>
                    <span class="help-block">그룹관리자에게 메일을 발송합니다.</span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="emailWriteBoardAdmin">게시판관리자</label>
                </th>
                <td class="table_body">
                    <input type="checkbox" name="emailWriteBoardAdmin" id="emailWriteBoardAdmin" value="1" @if($configEmailBoard->emailWriteBoardAdmin == 1) checked @endif>
                        <label for="emailWriteBoardAdmin">사용</label>
                    <span class="help-block">게시판관리자에게 메일을 발송합니다.</span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="emailWriter">원글작성자</label>
                </th>
                <td class="table_body">
                    <input type="checkbox" name="emailWriter" id="emailWriter" value="1" @if($configEmailBoard->emailWriter == 1) checked @endif>
                        <label for="emailWriter">사용</label>
                    <span class="help-block">게시자님께 메일을 발송합니다.</span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="emailAllCommenter">댓글작성자</label>
                </th>
                <td class="table_body">
                    <input type="checkbox" name="emailAllCommenter" id="emailAllCommenter" value="1" @if($configEmailBoard->emailAllCommenter == 1) checked @endif>
                        <label for="emailAllCommenter">사용</label>
                    <span class="help-block">원글에 댓글이 올라오는 경우 댓글 쓴 모든 분들께 메일을 발송합니다.</span>
                </td>
            </tr>
        </table>
    </section>
    <section id="cfs_mail_join" class="adm_box">
        <div class="adm_box_hd">
            <span class="adm_box_title">회원가입 시 메일 설정</span>
        </div>
        <table class="adm_box_table">
            <tr>
                <th>
                    <label for="emailJoinSuperAdmin">최고관리자</label>
                </th>
                <td class="table_body">
                    <input type="checkbox" name="emailJoinSuperAdmin" id="emailJoinSuperAdmin" value="1" @if($configEmailJoin->emailJoinSuperAdmin == 1) checked @endif>
                        <label for="emailJoinSuperAdmin">사용</label>
                    <span class="help-block">최고관리자에게 메일을 발송합니다.</span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="emailJoinUser">가입한 회원</label>
                </th>
                <td class="table_body">
                    <input type="checkbox" name="emailJoinUser" id="emailJoinUser" value="1" @if($configEmailJoin->emailJoinUser == 1) checked @endif>
                        <label for="emailJoinUser">사용</label>
                    <span class="help-block">회원가입한 회원님께 메일을 발송합니다.</span>
                </td>
            </tr>
        </table>
    </section>
    <section id="cfs_sns" class="adm_box">
        <div class="adm_box_hd">
            <span class="adm_box_title">소셜로그인</span>
        </div>
        <table class="adm_box_table">
            <tr>
                <th>네이버 Key</th>
                <td class="table_body">
                    <input type="text" class="form-control form_large" name="naverKey" value="{{ $configSns->naverKey }}" style="display: inline-block;">
                    <a href="https://developers.naver.com/apps/#/register" class="btn btn-sir ml15" target="_blank">앱 등록하기</a>
                </td>
            </tr>
            <tr>
                <th>네이버 Secret</th>
                <td class="table_body">
                    <input type="text" class="form-control form_large" name="naverSecret" value="{{ $configSns->naverSecret }}">
                </td>
            </tr>
            <tr>
                <th>네이버 Redirect URI</th>
                <td class="table_body">
                    <input type="text" class="form-control form_large" name="naverRedirect" value="{{ $configSns->naverRedirect }}">
                </td>
            </tr>
            <tr>
                <th>카카오 Key</th>
                <td class="table_body">
                    <input type="text" class="form-control form_large" name="kakaoKey" value="{{ $configSns->kakaoKey }}" style="display: inline-block;">
                    <a href="https://developers.kakao.com/" class="btn btn-sir ml15" target="_blank">앱 등록하기</a>
                </td>
            </tr>
            <tr>
                <th>카카오 Secret</th>
                <td class="table_body">
                    <input type="text" class="form-control form_large" name="kakaoSecret" value="{{ $configSns->kakaoSecret }}">
                </td>
            </tr>
            <tr>
                <th>카카오 Redirect URI</th>
                <td class="table_body">
                    <input type="text" class="form-control form_large" name="kakaoRedirect" value="{{ $configSns->kakaoRedirect }}">
                </td>
            </tr>
            <tr>
                <th>페이스북 Key</th>
                <td class="table_body">
                    <input type="text" class="form-control form_large" name="facebookKey" value="{{ $configSns->facebookKey }}" style="display: inline-block;">
                    <a href="https://developers.facebook.com/" class="btn btn-sir ml15" target="_blank">앱 등록하기</a>
                </td>
            </tr>
            <tr>
                <th>페이스북 Secret</th>
                <td class="table_body">
                    <input type="text" class="form-control form_large" name="facebookSecret" value="{{ $configSns->facebookSecret }}">
                </td>
            </tr>
            <tr>
                <th>페이스북 Redirect URI</th>
                <td class="table_body">
                    <input type="text" class="form-control form_large" name="facebookRedirect" value="{{ $configSns->facebookRedirect }}">
                </td>
            </tr>
            <tr>
                <th>구글 Key</th>
                <td class="table_body">
                    <input type="text" class="form-control form_large" name="googleKey" value="{{ $configSns->googleKey }}" style="display: inline-block;">
                    <a href="https://console.developers.google.com" class="btn btn-sir ml15" target="_blank">앱 등록하기</a>
                </td>
            </tr>
            <tr>
                <th>구글 Secret</th>
                <td class="table_body">
                    <input type="text" class="form-control form_large" name="googleSecret" value="{{ $configSns->googleSecret }}">
                </td>
            </tr>
            <tr>
                <th>구글 Redirect URI</th>
                <td class="table_body">
                    <input type="text" class="form-control form_large" name="googleRedirect" value="{{ $configSns->googleRedirect }}">
                </td>
            </tr>
        </table>
    </section>
    <section id="cfs_extra" class="adm_box">
        <div class="adm_box_hd">
            <span class="adm_box_title">여분필드</span>
        </div>
        <table class="adm_box_table">
            <tr>
                <td class="table_body" colspan="2">
                    각 게시판 관리에서 개별적으로 설정 가능합니다.
                </td>
            </tr>
            @for($i=1; $i<=10; $i++)
            <tr>
                <th>여분필드{{ $i }}</th>
                <td class="table_body">
                    제목 <input type="text" class="form-control form_middle" name="subj_{{ $i}}" value="{{ $configExtra["subj_$i"] }}">
                    값 <input type="text" class="form-control form_middle" name="value_{{ $i}}" value="{{ $configExtra["value_$i"] }}">
                </td>
            </tr>
            @endfor
        </table>
    </section>
    <section id="bottom"></section>
    </form>
</div>
@endsection
