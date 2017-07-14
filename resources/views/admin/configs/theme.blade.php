@extends('admin.admin')

@section('title')
    테마 설정 | {{ cache('config.homepage')->title }}
@endsection

@section('include_script')
    <script src="{{ asset('js/common.js') }}"></script>
    <script src="{{ asset('js/theme.js') }}"></script>
    <script>
        var menuVal = 100300
        function formSubmit() {
            $("#skinForm").submit();
        }
    </script>
@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>테마설정</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">환경설정</li>
            <li class="depth">테마설정</li>
        </ul>
    </div>
    <div class="pull-right">
        <ul class="mb_btn" style="margin-top:8px;">
            <li>
                <button type="button" class="btn btn-default" onclick="formSubmit();">개별 스킨 저장</button>
            </li>
        </ul>
    </div>
</div>
<div id="body_tab_type2">
    <span class="txt">설치된 테마 : {{ count($themes) }}</span>
</div>
<div class="body-contents">
    <ul class="theme_list">
        @if(count($themes) > 0)
        @foreach($themes as $theme)
            <li class="themebox">
                <div class="tmli_if">
                    <span class="img">
                        <img src="{{ asset("images/screenshot_".$theme['name'].".png") }}">
                    </span>
                    <span class="txt">{{ $theme['info']['themeName'] }}</span>
                </div>
                <div class="tmli_btn">
                    @if($theme['name'] == cache('config.theme')->name)
                        <span class="theme_sl use">사용중</span>
                    @else
                        <button class="theme_sl use_apply" data-theme="{{ $theme['name'] }}" data-name="{{ $theme['info']['themeName'] }}">테마적용</button>
                    @endif
                    <a href="#" class="theme_pr">미리보기</a>
                    <button class="theme_preview" data-theme="{{ $theme['name'] }}">상세보기</button>
                </div>
            </li>
        @endforeach
        @else

        @endif
    </ul>
</div>
<form method="post" role="form" id="skinForm" action="{{ route('admin.themes.update.skin')}}">
    {{ csrf_field() }}
<div class="body-contents">
    <div class="form-group">
        <label for="layoutSkin" class="col-md-2 control-label">홈페이지 레이아웃 스킨설정</label>
        <div class="col-md-2">
            <select class="form-control" name="layoutSkin" id="layoutSkin">
                @foreach($layoutSkins as $skin)
                    <option value='{{ $skin }}' @if(cache('config.skin')->layout == $skin) selected @endif>
                        {{ $skin }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="body-contents">
    <div class="form-group">
        <label for="boardSkin" class="col-md-2 control-label">전체 게시판 스킨설정</label>
        개별 게시판 스킨 설정은 게시판 관리에서 할 수 있습니다.
        <div class="col-md-2">
            <select class="form-control" name="boardSkin" id="boardSkin">
                @foreach($boardSkins as $skin)
                    <option value='{{ $skin }}' @if(cache('config.skin')->board == $skin) selected @endif>
                        {{ $skin }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="body-contents">
    <div class="form-group">
        <label for="userSkin" class="col-md-2 control-label">회원/로그인 스킨설정</label>
        <div class="col-md-2">
            <select class="form-control" name="userSkin" id="userSkin">
                @foreach($userSkins as $skin)
                    <option value='{{ $skin }}' @if(cache('config.join')->skin == $skin) selected @endif>
                        {{ $skin }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="body-contents">
    <div class="form-group">
        <label for="latestSkin" class="col-md-2 control-label">최근 게시물(메인노출) 스킨설정</label>
        <div class="col-md-2">
            <select class="form-control" name="latestSkin" id="latestSkin">
                @foreach($latestSkins as $skin)
                    <option value='{{ $skin }}' @if(cache('config.skin')->latest == $skin) selected @endif>
                        {{ $skin }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="body-contents">
    <div class="form-group">
        <label for="newSkin" class="col-md-2 control-label">새글 스킨설정</label>
        <div class="col-md-2">
            <select class="form-control" name="newSkin" id="newSkin">
                @foreach($newSkins as $skin)
                    <option value='{{ $skin }}' @if(cache('config.homepage')->newSkin == $skin) selected @endif>
                        {{ $skin }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="body-contents">
    <div class="form-group">
        <label for="searchSkin" class="col-md-2 control-label">전체 검색 스킨설정</label>
        <div class="col-md-2">
            <select class="form-control" name="searchSkin" id="searchSkin">
                @foreach($searchSkins as $skin)
                    <option value='{{ $skin }}' @if(cache('config.homepage')->searchSkin == $skin) selected @endif>
                        {{ $skin }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="body-contents">
    <div class="form-group">
        <label for="contentSkin" class="col-md-2 control-label">내용 관리 스킨설정</label>
        개별 항목의 스킨 설정은 내용 관리에서 할 수 있습니다.
        <div class="col-md-2">
            <select class="form-control" name="contentSkin" id="contentSkin">
                @foreach($contentSkins as $skin)
                    <option value='{{ $skin }}' @if(cache('config.skin')->content == $skin) selected @endif>
                        {{ $skin }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="body-contents">
    <div class="form-group">
        <label for="mailSkin" class="col-md-2 control-label">메일 양식 스킨설정</label>
        <div class="col-md-2">
            <select class="form-control" name="mailSkin" id="mailSkin">
                @foreach($mailSkins as $skin)
                    <option value='{{ $skin }}' @if(cache('config.skin')->mail == $skin) selected @endif>
                        {{ $skin }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="body-contents">
    <div class="form-group">
        <label for="memo" class="col-md-2 control-label">쪽지 스킨설정</label>
        <div class="col-md-2">
            <select class="form-control" name="memo" id="memo">
                @foreach($memoSkins as $skin)
                    <option value='{{ $skin }}' @if(cache('config.skin')->memo == $skin) selected @endif>
                        {{ $skin }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>
</form>
@endsection
