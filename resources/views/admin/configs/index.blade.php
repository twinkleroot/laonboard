@extends('theme')

@section('title')
    환경 설정 | LaBoard
@endsection

@section('content')
@if(Session::has('message'))
  <div class="alert alert-info">
    {{ Session::get('message') }}
  </div>
@endif
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading"><h2>환경 설정</h2></div>
            <div class="panel-heading">회원 가입 설정</div>
            <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.config.update', ['name' => 'join']) }}">
                {{ method_field('PUT') }}
                {{ csrf_field() }}
                <div class="panel-body">
                    <div class="form-group">
                        <label for="emailCertify" class="col-md-4 control-label">이메일 인증 사용</label>

                        <div class="col-md-6">
                            <input type="checkbox" name="emailCertify" id="emailCertify" value="1" @if($configJoin->emailCertify == 1) checked @endif>
                                <label for="emailCertify">사용</label>
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
                        <label for="openDate" class="col-md-4 control-label">정보공개 수정</label>

                        <div class="col-md-6">
                            수정하면 <input type="text" name="openDate" value="{{ $configJoin->openDate }}">일 동안 바꿀 수 없음
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="email" class="col-md-4 control-label">이름</label>

                        <div class="col-md-6">
                            <input type="radio" name="name" id="name_check" value="1" @if($configJoin->name == 1) checked @endif>
                                <label for="name_check">선택</label>
                            <input type="radio" name="name" id="name_uncheck" value="0" @if($configJoin->name == 0) checked @endif>
                                <label for="name_uncheck">해제</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="homepage" class="col-md-4 control-label">홈페이지</label>

                        <div class="col-md-6">
                            <input type="radio" name="homepage" id="homepage_check" value="1" @if($configJoin->homepage == 1) checked @endif>
                                <label for="homepage_check">선택</label>
                            <input type="radio" name="homepage" id="homepage_uncheck" value="0" @if($configJoin->homepage == 0) checked @endif>
                                <label for="homepage_uncheck">해제</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="tel" class="col-md-4 control-label">전화번호</label>

                        <div class="col-md-6">
                            <input type="radio" name="tel" id="tel_check" value="1" @if($configJoin->tel == 1) checked @endif>
                                <label for="tel_check">선택</label>
                            <input type="radio" name="tel" id="tel_uncheck" value="0" @if($configJoin->tel == 0) checked @endif>
                                <label for="tel_uncheck">해제</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="hp" class="col-md-4 control-label">휴대폰번호</label>

                        <div class="col-md-6">
                            <input type="radio" name="hp" id="hp_check" value="1" @if($configJoin->hp == 1) checked @endif>
                                <label for="hp_check">선택</label>
                            <input type="radio" name="hp" id="hp_uncheck" value="0" @if($configJoin->hp == 0) checked @endif>
                                <label for="hp_uncheck">해제</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="addr" class="col-md-4 control-label">주소</label>

                        <div class="col-md-6">
                            <input type="radio" name="addr" id="addr_check" value="1" @if($configJoin->addr == 1) checked @endif>
                                <label for="addr_check">선택</label>
                            <input type="radio" name="addr" id="addr_uncheck" value="0" @if($configJoin->addr == 0) checked @endif>
                                <label for="addr_uncheck">해제</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="signature" class="col-md-4 control-label">서명</label>

                        <div class="col-md-6">
                            <input type="radio" name="signature" id="signature_check" value="1" @if($configJoin->signature == 1) checked @endif>
                                <label for="signature_check">선택</label>
                            <input type="radio" name="signature" id="signature_uncheck" value="0" @if($configJoin->signature == 0) checked @endif>
                                <label for="signature_uncheck">해제</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="profile" class="col-md-4 control-label">자기소개</label>

                        <div class="col-md-6">
                            <input type="radio" name="profile" id="profile_check" value="1" @if($configJoin->profile == 1) checked @endif>
                                <label for="profile_check">선택</label>
                            <input type="radio" name="profile" id="profile_uncheck" value="0" @if($configJoin->profile == 0) checked @endif>
                                <label for="profile_uncheck">해제</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="recommend" class="col-md-4 control-label">추천인</label>

                        <div class="col-md-6">
                            <input type="radio" name="recommend" id="recommend_check" value="1" @if($configJoin->recommend == 1) checked @endif>
                                <label for="recommend_check">선택</label>
                            <input type="radio" name="recommend" id="recommend_uncheck" value="0" @if($configJoin->recommend == 0) checked @endif>
                                <label for="recommend_uncheck">해제</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="joinLevel" class="col-md-4 control-label">가입시 권한</label>

                        <div class="col-md-6">
                            <select name='joinLevel' class='level'>
                                @for ($i=1; $i<=10; $i++)
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
                        <label for="joinPoint" class="col-md-4 control-label">가입시 지급 포인트</label>

                        <div class="col-md-6">
                            <input type="text" name="joinPoint" value="{{ $configJoin->joinPoint }}">
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
                        <label for="loginPoint" class="col-md-4 control-label">로그인시 포인트</label>

                        <div class="col-md-6">
                            <input type="text" name="loginPoint" value="{{ $configJoin->loginPoint }}">
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

                <div class="panel-body">
                    <div class="col-md-offset-5">
                        <input type="submit" class="btn btn-primary" value="회원 가입 설정 변경하기"/>
                    </div>
                </div>
            </form>
            <div class="panel-heading">게시판 기본 설정</div>
            <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.config.update', ['name' => 'board']) }}">
                {{ method_field('PUT') }}
                {{ csrf_field() }}
                <div class="panel-body">
                    <div class="form-group">
                        <label for="delaySecond" class="col-md-4 control-label">글쓰기 간격</label>

                        <div class="col-md-6">
                            <input type="text" name="delaySecond" value="{{ $configBoard->delaySecond }}">초 지난후 가능
                        </div>
                    </div>
                </div>
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
@endsection
