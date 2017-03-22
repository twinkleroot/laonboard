@extends('theme')

@section('title')
    LaBoard | 환경 설정
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
            <div class="panel-heading">환경 설정</div>
            <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.config.update')}}">
                {{ method_field('PUT') }}
                {{ csrf_field() }}
                <div class="panel-body">
                    <div class="form-group">
                        <label for="nickDate" class="col-md-4 control-label">닉네임 수정</label>

                        <div class="col-md-6">
                            수정하면 <input type="text" name="nickDate" value="{{ $config->nickDate }}">일 동안 바꿀 수 없음
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="openDate" class="col-md-4 control-label">정보공개 수정</label>

                        <div class="col-md-6">
                            수정하면 <input type="text" name="openDate" value="{{ $config->openDate }}">일 동안 바꿀 수 없음
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="email" class="col-md-4 control-label">이름</label>

                        <div class="col-md-6">
                            <input type="radio" name="name" id="name_check" value="1" @if($config->name == 1) checked @endif>
                                <label for="name_check">선택</label>
                            <input type="radio" name="name" id="name_uncheck" value="0" @if($config->name == 0) checked @endif>
                                <label for="name_uncheck">해제</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="homepage" class="col-md-4 control-label">홈페이지</label>

                        <div class="col-md-6">
                            <input type="radio" name="homepage" id="homepage_check" value="1" @if($config->homepage == 1) checked @endif>
                                <label for="homepage_check">선택</label>
                            <input type="radio" name="homepage" id="homepage_uncheck" value="0" @if($config->homepage == 0) checked @endif>
                                <label for="homepage_uncheck">해제</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="tel" class="col-md-4 control-label">전화번호</label>

                        <div class="col-md-6">
                            <input type="radio" name="tel" id="tel_check" value="1" @if($config->tel == 1) checked @endif>
                                <label for="tel_check">선택</label>
                            <input type="radio" name="tel" id="tel_uncheck" value="0" @if($config->tel == 0) checked @endif>
                                <label for="tel_uncheck">해제</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="hp" class="col-md-4 control-label">휴대폰번호</label>

                        <div class="col-md-6">
                            <input type="radio" name="hp" id="hp_check" value="1" @if($config->hp == 1) checked @endif>
                                <label for="hp_check">선택</label>
                            <input type="radio" name="hp" id="hp_uncheck" value="0" @if($config->hp == 0) checked @endif>
                                <label for="hp_uncheck">해제</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="addr" class="col-md-4 control-label">주소</label>

                        <div class="col-md-6">
                            <input type="radio" name="addr" id="addr_check" value="1" @if($config->addr == 1) checked @endif>
                                <label for="addr_check">선택</label>
                            <input type="radio" name="addr" id="addr_uncheck" value="0" @if($config->addr == 0) checked @endif>
                                <label for="addr_uncheck">해제</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="signature" class="col-md-4 control-label">서명</label>

                        <div class="col-md-6">
                            <input type="radio" name="signature" id="signature_check" value="1" @if($config->signature == 1) checked @endif>
                                <label for="signature_check">선택</label>
                            <input type="radio" name="signature" id="signature_uncheck" value="0" @if($config->signature == 0) checked @endif>
                                <label for="signature_uncheck">해제</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="profile" class="col-md-4 control-label">자기소개</label>

                        <div class="col-md-6">
                            <input type="radio" name="profile" id="profile_check" value="1" @if($config->profile == 1) checked @endif>
                                <label for="profile_check">선택</label>
                            <input type="radio" name="profile" id="profile_uncheck" value="0" @if($config->profile == 0) checked @endif>
                                <label for="profile_uncheck">해제</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="recommend" class="col-md-4 control-label">추천인</label>

                        <div class="col-md-6">
                            <input type="radio" name="recommend" id="recommend_check" value="1" @if($config->recommend == 1) checked @endif>
                                <label for="recommend_check">선택</label>
                            <input type="radio" name="recommend" id="recommend_uncheck" value="0" @if($config->recommend == 0) checked @endif>
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
                                    <option value='{{ $i }}' @if($config->joinLevel == $i) selected @endif>
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
                            <input type="text" name="joinPoint" value="{{ $config->joinPoint }}">
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="recommendPoint" class="col-md-4 control-label">추천인 지급 포인트</label>

                        <div class="col-md-6">
                            <input type="text" name="recommendPoint" value="{{ $config->recommendPoint }}">
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="banId" class="col-md-4 control-label">아이디,닉네임 금지단어</label>

                        <div class="col-md-6">
                            <textarea cols="70" rows="5" name="banId">{{ $config->banId[0] }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="stipulation" class="col-md-4 control-label">회원가입약관</label>

                        <div class="col-md-6">
                            <textarea cols="50" rows="5" name="stipulation">{{ $config->stipulation }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="privacy" class="col-md-4 control-label">개인정보처리방침</label>

                        <div class="col-md-6">
                            <textarea cols="50" rows="5" name="privacy">{{ $config->privacy }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="col-md-offset-5">
                        <input type="submit" class="btn btn-primary" value="변경하기"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
