@extends('admin.admin')

@section('title')
    환경 설정 | {{ $configHomepage->title }}
@endsection

@section('include_script')
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
    });

    $(document).ready(function(){
        $("#body_tab_type2 li").click(function () {
            $('.adm_box').hide().eq($(this).index()).show();
        });
    });
</script>
@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>기본환경설정</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">환경설정</li>
            <li class="depth">기본환경설정</li>
        </ul>
    </div>
</div>

<div id="body_tab_type2">
    <ul>
        <li class="tab"><a href="#admin-header">기본환경설정</a></li>
        <li class="tab"><a href="#admin-header">게시판기본</a></li>
        <li class="tab"><a href="#admin-header">회원가입</a></li>
        <li class="tab"><a href="#admin-header">본인확인</a></li>
        <li class="tab"><a href="#admin-header">메일환경</a></li>
        <li class="tab"><a href="#admin-header">글작성시 메일</a></li>
        <li class="tab"><a href="#admin-header">회원가입시 메일</a></li>
    </ul>

    <div class="submit_btn">
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

    <div id="admin_box1">
        <form role="form" method="POST" action="{{ route('admin.config.update', ['name' => 'homepage']) }}">
        {{ method_field('PUT') }}
        {{ csrf_field() }}
        <div class="adm_panel">
            <div class="adm_box_hd">
                <span class="adm_box_title">기본 환경설정</span>
            </div>
            <div class="adm_box_bd">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="title" class="col-md-2 control-label">홈페이지 제목</label>
                        <div class="col-md-5">
                            <input type="text" class="required form-control" name="title" value="{{ $configHomepage->title }}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="superAdmin" class="col-md-2 control-label">최고관리자</label>
                        <div class="col-md-5">
                            <select class="form-control" name="superAdmin">
                                <option value='' @if($configHomepage->superAdmin == '') selected @endif>
                                    선택안함
                                </option>
                                @foreach($admins as $admin)
                                    <option value='{{ $admin->email }}' @if($configHomepage->superAdmin == $admin->email) selected @endif>
                                        {{ $admin->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="usePoint" class="col-md-2 control-label">포인트 사용</label>
                        <div class="col-md-3">
                            <input type="checkbox" name="usePoint" id="usePoint" value="1" @if($configHomepage->usePoint == 1) checked @endif>
                            <label for="usePoint">사용</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="loginPoint" class="col-md-2 control-label">로그인시 포인트</label>
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="loginPoint" value="{{ $configHomepage->loginPoint }}">
                            <p class="help-block">회원이 로그인시 하루에 한번만 적립</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="memoSendPoint" class="col-md-2 control-label">쪽지보낼시 차감 포인트</label>
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="memoSendPoint" value="{{ $configHomepage->memoSendPoint }}">
                            <p class="help-block">양수로 입력하십시오. 0점은 쪽지 보낼시 포인트를 차감하지 않습니다.</p>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="openDate" class="col-md-4 control-label">정보공개 수정</label>

                            <div class="col-md-6">
                                수정하면 <input type="text" name="openDate" value="{{ $configHomepage->openDate }}">일 동안 바꿀 수 없음
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="newDel" class="col-md-4 control-label">최근게시물 삭제</label>

                            <div class="col-md-6">
                                설정일이 지난 최근게시물 자동 삭제<br>
                                <input type="text" name="newDel" value="{{ $configHomepage->newDel }}">
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="memoDel" class="col-md-4 control-label">쪽지 삭제</label>

                            <div class="col-md-6">
                                설정일이 지난 쪽지 자동 삭제<br>
                                <input type="text" name="memoDel" value="{{ $configHomepage->memoDel }}">
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="popularDel" class="col-md-4 control-label">인기검색어 삭제</label>

                            <div class="col-md-6">
                                설정일이 지난 인기검색어 자동 삭제<br>
                                <input type="text" name="popularDel" value="{{ $configHomepage->popularDel }}">
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="newRows" class="col-md-4 control-label">최근게시물 라인수</label>

                            <div class="col-md-6">
                                목록 한 페이지당 라인수<br />
                                <input type="text" name="newRows" value="{{ $configHomepage->newRows }}">라인
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="pageRows" class="col-md-4 control-label">한 페이지당 라인수</label>

                            <div class="col-md-6">
                                목록(리스트) 한 페이지당 라인수<br />
                                <input type="text" name="pageRows" value="{{ $configHomepage->pageRows }}">라인
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="mobilePageRows" class="col-md-4 control-label">모바일 한 페이지당 라인수</label>

                            <div class="col-md-6">
                                목록 한 페이지당 라인수<br />
                                <input type="text" name="mobilePageRows" value="{{ $configHomepage->mobilePageRows }}">라인
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="writePages" class="col-md-4 control-label">페이지 표시 수</label>

                            <div class="col-md-6">
                                <input type="text" name="writePages" value="{{ $configHomepage->writePages }}">페이지씩 표시
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="mobilePages" class="col-md-4 control-label">모바일 페이지 표시 수</label>

                                <div class="col-md-6">
                                    <input type="text" name="mobilePages" value="{{ $configHomepage->mobilePages }}">페이지씩 표시
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="newSkin" class="col-md-4 control-label">최근게시물 스킨</label>

                            <div class="col-md-6">
                                <select name='newSkin'>
                                    @foreach($latestSkins as $key => $value)
                                        <option value='{{ $key }}' @if($configHomepage->newSkin == $key) selected @endif>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="searchSkin" class="col-md-4 control-label">검색 스킨</label>

                            <div class="col-md-6">
                                <select name='searchSkin'>
                                    @foreach($searchSkins as $key => $value)
                                        <option value='{{ $key }}' @if($configHomepage->searchSkin == $key) selected @endif>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="useCopyLog" class="col-md-2 control-label">복사, 이동시 로그</label>
                            <div class="col-md-3">
                                게시물 아래에 누구로 부터 복사, 이동됨 표시<br>
                                <input type="checkbox" name="useCopyLog" id="useCopyLog" value="1" @if($configHomepage->useCopyLog == 1) checked @endif>
                                <label for="useCopyLog">남김</label>
                            </div>
                        </div>
                    </div>	
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="pointTerm" class="col-md-4 control-label">포인트 유효기간</label>

                            <div class="col-md-6">
                                기간을 0으로 설정시 포인트 유효기간이 적용되지 않습니다.<br />
                                <input type="text" name="pointTerm" value="{{ $configHomepage->pointTerm }}">일
                            </div>
                        </div>
                    </div>

                    <input type="submit" class="btn btn-sir" value="설정변경"/>
                </div>
            </div>
        </form>
    </div>

    <div id="admin_box2">
        <div class="panel panel-default">
        <div class="panel-body">
                <div class="panel-heading">게시판 기본 설정</div>
            <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.config.update', ['name' => 'board']) }}">
                {{ method_field('PUT') }}
                {{ csrf_field() }}
                <div class="panel-body">
                    <div class="form-group">
                        <label for="linkTarget" class="col-md-4 control-label">새창 링크</label>

                        <div class="col-md-6">
                            글내용중 자동 링크되는 타켓을 지정합니다.
                            <select name="linkTarget">
                                <option value="_blank" @if($configBoard->linkTarget == '_blank') selected @endif>_blank</option>
                                <option value="_self" @if($configBoard->linkTarget == '_self') selected @endif>_self</option>
                                <option value="_top" @if($configBoard->linkTarget == '_top') selected @endif>_top</option>
                                <option value="_new" @if($configBoard->linkTarget == '_new') selected @endif>_new</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="readPoint" class="col-md-4 control-label">글읽기 포인트</label>

                        <div class="col-md-6">
                            <input type="text" name="readPoint" value="{{ $configBoard->readPoint }}">점
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="writePoint" class="col-md-4 control-label">글쓰기 포인트</label>

                        <div class="col-md-6">
                            <input type="text" name="writePoint" value="{{ $configBoard->writePoint }}">점
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="commentPoint" class="col-md-4 control-label">댓글쓰기 포인트</label>

                        <div class="col-md-6">
                            <input type="text" name="commentPoint" value="{{ $configBoard->commentPoint }}">점
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="downloadPoint" class="col-md-4 control-label">다운로드 포인트</label>

                        <div class="col-md-6">
                            <input type="text" name="downloadPoint" value="{{ $configBoard->downloadPoint }}">점
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="searchPart" class="col-md-4 control-label">검색 단위</label>

                        <div class="col-md-6">
                            <input type="text" name="searchPart" value="{{ $configBoard->searchPart }}">건 단위로 검색
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="imageExtension" class="col-md-4 control-label">이미지 업로드 확장자</label>

                        <div class="col-md-6">
                            게시판 글작성시 이미지 파일 업로드 가능 확장자. | 로 구분<br />
                            <input type="text" name="imageExtension" value="{{ $configBoard->imageExtension }}">
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="flashExtension" class="col-md-4 control-label">플래쉬 업로드 확장자</label>

                        <div class="col-md-6">
                            게시판 글작성시 플래쉬 파일 업로드 가능 확장자. | 로 구분<br />
                            <input type="text" name="flashExtension" value="{{ $configBoard->flashExtension }}">
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="movieExtension" class="col-md-4 control-label">동영상 업로드 확장자</label>

                        <div class="col-md-6">
                            게시판 글작성시 동영상 파일 업로드 가능 확장자. | 로 구분<br />
                            <input type="text" name="movieExtension" value="{{ $configBoard->movieExtension }}">
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="filter" class="col-md-4 control-label">단어 필터링</label>

                        <div class="col-md-6">
                            입력된 단어가 포함된 내용은 게시할 수 없습니다. 단어와 단어 사이는 ,로 구분합니다.<br />
                            <textarea cols="80" rows="10" name="filter" >{{ $configBoard->filter[0] }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="panel-body">
                    <div class="col-md-offset-5">
                        <input type="submit" class="btn btn-primary" value="게시판 기본 설정 변경하기"/>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>

    <div id="admin_box3">
        <div class="adm_panel">
            <div class="adm_box_bd">
                <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.config.update', ['name' => 'join']) }}">
                {{ method_field('PUT') }}
                {{ csrf_field() }}
                    <div class="st_title">회원가입</div>
                    <div class="st_contents">
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="newSkin" class="col-md-4 control-label">회원 스킨</label>

                            <div class="col-md-6">
                                <select name='newSkin'>
                                    @foreach($userSkins as $key => $value)
                                        <option value='{{ $key }}' @if($configJoin->skin == $key) selected @endif>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="nickDate" class="col-md-4 control-label">닉네임 수정</label>

                            <div class="col-md-6">
                                수정하면 <input type="text" name="nickDate" value="{{ $configJoin->nickDate }}">일 동안 바꿀 수 없음
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="email" class="col-md-4 control-label">이름 입력</label>

                            <div class="col-md-6">
                                <input type="radio" name="name" id="name_check" value="1" @if($configJoin->name == 1) checked @endif>
                                    <label for="name_check">사용</label>
                                <input type="radio" name="name" id="name_uncheck" value="0" @if($configJoin->name == 0) checked @endif>
                                    <label for="name_uncheck">사용하지 않음</label>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="homepage" class="col-md-4 control-label">홈페이지 입력</label>

                            <div class="col-md-6">
                                <input type="radio" name="homepage" id="homepage_check" value="1" @if($configJoin->homepage == 1) checked @endif>
                                    <label for="homepage_check">사용</label>
                                <input type="radio" name="homepage" id="homepage_uncheck" value="0" @if($configJoin->homepage == 0) checked @endif>
                                    <label for="homepage_uncheck">사용하지 않음</label>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="tel" class="col-md-4 control-label">전화번호 입력</label>

                            <div class="col-md-6">
                                <input type="radio" name="tel" id="tel_check" value="1" @if($configJoin->tel == 1) checked @endif>
                                    <label for="tel_check">사용</label>
                                <input type="radio" name="tel" id="tel_uncheck" value="0" @if($configJoin->tel == 0) checked @endif>
                                    <label for="tel_uncheck">사용하지 않음</label>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="hp" class="col-md-4 control-label">휴대폰번호 입력</label>

                            <div class="col-md-6">
                                <input type="radio" name="hp" id="hp_check" value="1" @if($configJoin->hp == 1) checked @endif>
                                    <label for="hp_check">사용</label>
                                <input type="radio" name="hp" id="hp_uncheck" value="0" @if($configJoin->hp == 0) checked @endif>
                                    <label for="hp_uncheck">사용하지 않음</label>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="addr" class="col-md-4 control-label">주소 입력</label>

                            <div class="col-md-6">
                                <input type="radio" name="addr" id="addr_check" value="1" @if($configJoin->addr == 1) checked @endif>
                                    <label for="addr_check">사용</label>
                                <input type="radio" name="addr" id="addr_uncheck" value="0" @if($configJoin->addr == 0) checked @endif>
                                    <label for="addr_uncheck">사용하지 않음</label>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="signature" class="col-md-4 control-label">서명 입력</label>

                            <div class="col-md-6">
                                <input type="radio" name="signature" id="signature_check" value="1" @if($configJoin->signature == 1) checked @endif>
                                    <label for="signature_check">사용</label>
                                <input type="radio" name="signature" id="signature_uncheck" value="0" @if($configJoin->signature == 0) checked @endif>
                                    <label for="signature_uncheck">사용하지 않음</label>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="profile" class="col-md-4 control-label">자기소개 입력</label>

                            <div class="col-md-6">
                                <input type="radio" name="profile" id="profile_check" value="1" @if($configJoin->profile == 1) checked @endif>
                                    <label for="profile_check">사용</label>
                                <input type="radio" name="profile" id="profile_uncheck" value="0" @if($configJoin->profile == 0) checked @endif>
                                    <label for="profile_uncheck">사용하지 않음</label>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="joinLevel" class="col-md-4 control-label">회원가입시 권한</label>

                            <div class="col-md-6">
                                <select name='joinLevel' class='level'>
                                    @for ($i=1; $i<=9; $i++)
                                        <option value='{{ $i }}' @if($configJoin->joinLevel == $i) selected @endif>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="joinPoint" class="col-md-4 control-label">회원가입시 지급 포인트</label>

                            <div class="col-md-6">
                                <input type="text" name="joinPoint" value="{{ $configJoin->joinPoint }}">
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="leaveDay" class="col-md-4 control-label">회원탈퇴후 삭제일</label>

                            <div class="col-md-6">
                                <input type="text" name="leaveDay" value="{{ $configJoin->leaveDay }}">일 후 자동 삭제
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="useMemberIcon" class="col-md-4 control-label">회원아이콘 사용</label>

                            <div class="col-md-6">
                                게시물에 게시자 닉네임 대신 아이콘 사용
                                <select name='useMemberIcon' class='level'>
                                    <option value='0' @if($configJoin->useMemberIcon == 0) selected @endif>미사용</option>
                                    <option value='1' @if($configJoin->useMemberIcon == 1) selected @endif>아이콘만 표시</option>
                                    <option value='2' @if($configJoin->useMemberIcon == 2) selected @endif>아이콘+닉네임 표시</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="iconLevel" class="col-md-4 control-label">아이콘 업로드 권한</label>

                            <div class="col-md-6">
                                <select name='iconLevel' class='level'>
                                    @for ($i=1; $i<=9; $i++)
                                        <option value='{{ $i }}' @if($configJoin->iconLevel == $i) selected @endif>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="memberIconSize" class="col-md-4 control-label">회원아이콘 용량</label>

                            <div class="col-md-6">
                                <input type="text" name="memberIconSize" value="{{ $configJoin->memberIconSize }}">바이트 이하
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="memberIconWidthAndHeigth" class="col-md-4 control-label">회원아이콘 사이즈</label>

                            <div class="col-md-6">
                                가로<input type="text" name="memberIconWidth" value="{{ $configJoin->memberIconWidth }}">세로
                                <input type="text" name="memberIconHeight" value="{{ $configJoin->memberIconHeight }}">픽셀이하
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="recommend" class="col-md-4 control-label">추천인 제도</label>

                            <div class="col-md-6">
                                <input type="radio" name="recommend" id="recommend_check" value="1" @if($configJoin->recommend == 1) checked @endif>
                                    <label for="recommend_check">사용</label>
                                <input type="radio" name="recommend" id="recommend_uncheck" value="0" @if($configJoin->recommend == 0) checked @endif>
                                    <label for="recommend_uncheck">사용하지 않음</label>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="recommendPoint" class="col-md-4 control-label">추천인 지급 포인트</label>

                            <div class="col-md-6">
                                <input type="text" name="recommendPoint" value="{{ $configJoin->recommendPoint }}">
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="banId" class="col-md-4 control-label">닉네임 금지단어</label>

                            <div class="col-md-6">
                                <textarea cols="80" rows="5" name="banId">{{ $configJoin->banId[0] }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="stipulation" class="col-md-4 control-label">회원가입약관</label>

                            <div class="col-md-6">
                                <textarea cols="80" rows="5" name="stipulation">{{ $configJoin->stipulation }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="privacy" class="col-md-4 control-label">개인정보처리방침</label>

                            <div class="col-md-6">
                                <textarea cols="80" rows="5" name="privacy">{{ $configJoin->privacy }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="privacy" class="col-md-4 control-label">비밀번호 조합 정책</label>

                            <div class="col-md-6">
                                최소<input type="text" id="digits" name="passwordPolicyDigits"
                                    value="{{ $configJoin->passwordPolicyDigits }}" placeholder="비밀 번호 최소 자릿수를 입력해 주세요." />
                                    자릿수 이상 <br />
                                <input type="checkbox" id="special" name="passwordPolicySpecial" value="1"
                                    @if($configJoin->passwordPolicySpecial == 1) checked @endif/>
                                    <label for="special">특수문자 하나 이상</label> <br />
                                <input type="checkbox" id="upper" name="passwordPolicyUpper" value="1"
                                    @if($configJoin->passwordPolicyUpper == 1) checked @endif/>
                                    <label for="upper">대문자 하나 이상</label> <br />
                                <input type="checkbox" id="number" name="passwordPolicyNumber" value="1"
                                    @if($configJoin->passwordPolicyNumber == 1) checked @endif/>
                                    <label for="number">숫자 하나 이상</label> <br />
                            </div>
                        </div>
                    </div>
                    <input type="submit" class="btn btn-sir" value="설정변경"/>
                </form>
            </div>
        </div>
    </div>

    <div id="admin_box4">
        <div class="adm_panel">
            <div class="adm_box_bd">
                <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.config.update', ['name' => 'cert']) }}">
                {{ method_field('PUT') }}
                {{ csrf_field() }}
                    <div class="st_title">본인확인 설정</div>
                    <div class="st_contents">
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="certUse" class="col-md-4 control-label">본인확인</label>

                            <div class="col-md-6">
                                <select name='certUse'>
                                    <option value='0' @if($configCert->certUse == 0) selected @endif>사용안함</option>
                                    <option value='1' @if($configCert->certUse == 1) selected @endif>테스트</option>
                                    <option value='2' @if($configCert->certUse == 2) selected @endif>실서비스</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="certIpin" class="col-md-4 control-label">아이핀 본인확인</label>

                            <select name='certIpin'>
                                <option value @if(!$configCert->certIpin) selected @endif>사용안함</option>
                                <option value='kcb' @if($configCert->certIpin == 'kcb') selected @endif>코리아크레딧뷰로(KCB) 아이핀</option>
                            </select>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="certHp" class="col-md-4 control-label">휴대폰 본인확인</label>

                            <select name='certHp'>
                                <option value @if(!$configCert->certHp) selected @endif>사용안함</option>
                                <option value='kcb' @if($configCert->certHp == 'kcb') selected @endif>코리아크레딧뷰로(KCB) 휴대폰 본인확인</option>
                                {{-- <option value='kcp' @if($configCert->certHp == 'kcp') selected @endif>NHN KCP 휴대폰 본인확인</option>
                                <option value='lg' @if($configCert->certHp == 'lg') selected @endif>LG유플러스 휴대폰 본인확인</option> --}}
                            </select>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="certKcbCd" class="col-md-4 control-label">코리아크레딧뷰로 KCB 회원사ID</label>

                            <div class="col-md-6">
                                KCB 회원사ID를 입력해 주십시오.<br />
                                서비스에 가입되어 있지 않다면, KCB와 계약체결 후 회원사ID를 발급 받으실 수 있습니다.<br />
                                이용하시려는 서비스에 대한 계약을 아이핀, 휴대폰 본인확인 각각 체결해주셔야 합니다.<br />
                                아이핀 본인확인 테스트의 경우에는 KCB 회원사ID가 필요 없으나,<br />
                                휴대폰 본인확인 테스트의 경우 KCB 에서 따로 발급 받으셔야 합니다.<br />
                                <input type="text" name="certKcbCd" value="{{ $configCert->certKcbCd }}">
                            </div>
                            <a href="http://sir.kr/main/service/b_ipin.php" target="_blank">KCB 아이핀 서비스 신청페이지</a>
                            <a href="http://sir.kr/main/service/b_cert.php" target="_blank">KCB 휴대폰 본인확인 서비스 신청페이지</a>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="certLimit" class="col-md-4 control-label">본인확인 이용제한</label>

                            <div class="col-md-6">
                                하루동안 아이핀과 휴대폰 본인확인 인증 이용회수를 제한할 수 있습니다.<br />
                                회수제한은 실서비스에서 아이핀과 휴대폰 본인확인 인증에 개별 적용됩니다.<br />
                                0 으로 설정하시면 회수제한이 적용되지 않습니다.<br />
                                <input type="text" name="certLimit" value="{{ $configCert->certLimit }}">회
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="certReq" class="col-md-4 control-label">본인확인 필수</label>

                            <div class="col-md-6">
                                회원가입 때 본인확인을 필수로 할지 설정합니다. 필수로 설정하시면 본인확인을 하지 않은 경우 회원가입이 안됩니다.<br />
                                <input type="checkbox" name="certReq" id="certReq" value="1" @if($configCert->certReq == 1) checked @endif>
                                    <label for="certReq">예</label>
                            </div>
                        </div>
                    </div>
                     <input type="submit" class="btn btn-sir" value="설정변경"/>
                </form>
            </div>
        </div>
    </div>

    <div id="admin_box5">
        <div class="panel panel-default">
        <div class="panel-body">
                <div class="panel-heading">기본 메일 환경 설정</div>
            <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.config.update', ['name' => 'email.default']) }}">
                {{ method_field('PUT') }}
                {{ csrf_field() }}
                <div class="panel-body">
                    <div class="form-group">
                        <label for="emailUse" class="col-md-4 control-label">메일발송 사용</label>

                        <div class="col-md-6">
                            체크하지 않으면 메일발송을 아예 사용하지 않습니다. 메일 테스트도 불가합니다.<br />
                            <input type="checkbox" name="emailUse" id="emailUse" value="1" @if($configEmailDefault->emailUse == 1) checked @endif>
                                <label for="emailUse">사용</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="emailCertify" class="col-md-4 control-label">이메일 인증 사용</label>

                        <div class="col-md-6">
                            메일에 배달된 인증 주소를 클릭하여야 회원으로 인정합니다.<br />
                            <input type="checkbox" name="emailCertify" id="emailCertify" value="1" @if($configEmailDefault->emailCertify == 1) checked @endif>
                                <label for="emailCertify">사용</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="formmailIsMember" class="col-md-4 control-label">폼메일 사용 여부</label>

                        <div class="col-md-6">
                            체크하지 않으면 비회원도 사용 할 수 있습니다.<br />
                            <input type="checkbox" name="formmailIsMember" id="formmailIsMember" value="1" @if($configEmailDefault->formmailIsMember == 1) checked @endif>
                                <label for="formmailIsMember">회원만 사용</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="col-md-offset-5">
                        <input type="submit" class="btn btn-primary" value="기본 메일 환경 설정 변경하기"/>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>
    <div id="admin_box6">
        <div class="panel panel-default">
        <div class="panel-body">
                <div class="panel-heading">게시판 글 작성시 메일 설정</div>
            <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.config.update', ['name' => 'email.board']) }}">
                {{ method_field('PUT') }}
                {{ csrf_field() }}
                <div class="panel-body">
                    <div class="form-group">
                        <label for="emailWriteSuperAdmin" class="col-md-4 control-label">최고관리자</label>

                        <div class="col-md-6">
                            최고관리자에게 메일을 발송합니다.<br />
                            <input type="checkbox" name="emailWriteSuperAdmin" id="emailWriteSuperAdmin" value="1" @if($configEmailBoard->emailWriteSuperAdmin == 1) checked @endif>
                                <label for="emailWriteSuperAdmin">사용</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="emailWriteGroupAdmin" class="col-md-4 control-label">그룹관리자</label>

                        <div class="col-md-6">
                            그룹관리자에게 메일을 발송합니다.<br />
                            <input type="checkbox" name="emailWriteGroupAdmin" id="emailWriteGroupAdmin" value="1" @if($configEmailBoard->emailWriteGroupAdmin == 1) checked @endif>
                                <label for="emailWriteGroupAdmin">사용</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="emailWriteBoardAdmin" class="col-md-4 control-label">게시판관리자</label>

                        <div class="col-md-6">
                            게시판관리자에게 메일을 발송합니다.<br />
                            <input type="checkbox" name="emailWriteBoardAdmin" id="emailWriteBoardAdmin" value="1" @if($configEmailBoard->emailWriteBoardAdmin == 1) checked @endif>
                                <label for="emailWriteBoardAdmin">사용</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="emailWriter" class="col-md-4 control-label">원글작성자</label>

                        <div class="col-md-6">
                            게시자님께 메일을 발송합니다.<br />
                            <input type="checkbox" name="emailWriter" id="emailWriter" value="1" @if($configEmailBoard->emailWriter == 1) checked @endif>
                                <label for="emailWriter">사용</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="emailAllCommenter" class="col-md-4 control-label">댓글작성자</label>

                        <div class="col-md-6">
                            원글에 댓글이 올라오는 경우 댓글 쓴 모든 분들께 메일을 발송합니다.<br />
                            <input type="checkbox" name="emailAllCommenter" id="emailAllCommenter" value="1" @if($configEmailBoard->emailAllCommenter == 1) checked @endif>
                                <label for="emailAllCommenter">사용</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="col-md-offset-5">
                        <input type="submit" class="btn btn-primary" value="게시판 글 작성시 메일 설정 변경하기"/>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>
    <div id="admin_box7">
        <div class="panel panel-default">
        <div class="panel-body">

            <div class="panel-heading">회원가입 시 메일 설정</div>
            <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.config.update', ['name' => 'email.join']) }}">
                {{ method_field('PUT') }}
                {{ csrf_field() }}
                <div class="panel-body">
                    <div class="form-group">
                        <label for="emailJoinSuperAdmin" class="col-md-4 control-label">최고관리자</label>

                        <div class="col-md-6">
                            최고관리자에게 메일을 발송합니다.<br />
                            <input type="checkbox" name="emailJoinSuperAdmin" id="emailJoinSuperAdmin" value="1" @if($configEmailJoin->emailJoinSuperAdmin == 1) checked @endif>
                                <label for="emailJoinSuperAdmin">사용</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="emailJoinUser" class="col-md-4 control-label">가입한 회원</label>

                        <div class="col-md-6">
                            회원가입한 회원님께 메일을 발송합니다.<br />
                            <input type="checkbox" name="emailJoinUser" id="emailJoinUser" value="1" @if($configEmailJoin->emailJoinUser == 1) checked @endif>
                                <label for="emailJoinUser">사용</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="col-md-offset-5">
                        <input type="submit" class="btn btn-primary" value="회원가입 시 메일 설정 변경하기"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </div>
</div>
@endsection
