@extends('admin.layouts.basic')

@section('title')테마 설정 | {{ cache('config.homepage')->title }}@endsection

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
<script src="{{ ver_asset('js/theme.js') }}"></script>
<script>
    var menuVal = 100300;

    function formSubmit() {
        $("#skinForm").submit();
    }

    $(document).ready(function(){
        $('.adm_box').hide().eq(0).show();

        $("#body_tab_type2 li").click(function () {
            $('.adm_box').hide().eq($(this).index()).show();
        });

        $(".tab").click(function () {
            $(".tab").removeClass("active");
            $(this).addClass("active");
        });
    });
</script>
@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>테마 설정</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">환경 설정</li>
            <li class="depth">테마 설정</li>
        </ul>
    </div>
</div>
<div id="body_tab_type2">
    <ul>
        <li class="tab"><a href="#theme">테마설정</a></li>
        <li class="tab"><a href="#skin">개별스킨설정</a></li>
    </ul>
    <div class="submit_btn">
        <button type="button" class="btn btn-default" onclick="formSubmit();">개별 스킨 저장</button>
    </div>
</div>
<div class="body-contents">
<section id="theme" class="adm_box">
    <p>설치된 테마 : {{ notNullCount($themes) }}</p>
    <ul class="theme_list">
        @forelse($themes as $theme)
        <li class="themebox">
            <div class="tmli_if">
                <span class="img">
                    <img src="{{ ver_asset("themes/".$theme['name']."/images/screenshot.png") }}">
                </span>
                <span class="txt">{{ $theme['info']['themeName'] }}</span>
            </div>
            <div class="tmli_btn">
                @if($theme['name'] == cache('config.theme')->name)
                <span class="theme_sl use">사용중</span>
                @else
                <button class="theme_sl use_apply" data-theme="{{ $theme['name'] }}" data-name="{{ $theme['info']['themeName'] }}">테마적용</button>
                @endif
                <a href="{{ route('admin.themes.preview.index', $theme['name']) }}" class="theme_pr" target="_blank">미리보기</a>
                <button class="theme_preview" data-theme="{{ $theme['name'] }}">상세보기</button>
            </div>
        </li>
        @empty
        @endforelse
    </ul>
</section>

<section id="skin" class="adm_box">
    <form method="post" role="form" id="skinForm" action="{{ route('admin.themes.update.skin')}}">
        {{ csrf_field() }}
        <div class="adm_panel">
            <div class="adm_box_hd">
                <span class="adm_box_title">개별 스킨 설정</span>
            </div>
            <div class="adm_box_bd">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="boardSkin" class="col-md-2 control-label">전체 게시판 스킨설정</label>
                        <div class="col-md-5">
                            <select class="form-control" name="boardSkin" id="boardSkin">
                                @foreach($boardSkins as $skin)
                                <option value='{{ $skin }}' @if(cache('config.skin')->board == $skin) selected @endif>
                                    {{ $skin }}
                                </option>
                                @endforeach
                            </select>
                            <span class="help-block">개별 게시판 스킨 설정은 게시판 관리에서 할 수 있습니다.</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="userSkin" class="col-md-2 control-label">회원/로그인 스킨설정</label>
                        <div class="col-md-5">
                            <select class="form-control" name="userSkin" id="userSkin">
                                @foreach($userSkins as $skin)
                                <option value='{{ $skin }}' @if(cache('config.join')->skin == $skin) selected @endif>
                                    {{ $skin }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="latestSkin" class="col-md-2 control-label">최근 게시물(메인노출) 스킨설정</label>
                        <div class="col-md-5">
                            <select class="form-control" name="latestSkin" id="latestSkin">
                                @foreach($latestSkins as $skin)
                                <option value='{{ $skin }}' @if(cache('config.skin')->latest == $skin) selected @endif>
                                    {{ $skin }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="newSkin" class="col-md-2 control-label">새글 스킨설정</label>
                        <div class="col-md-5">
                            <select class="form-control" name="newSkin" id="newSkin">
                                @foreach($newSkins as $skin)
                                <option value='{{ $skin }}' @if(cache('config.homepage')->newSkin == $skin) selected @endif>
                                    {{ $skin }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="searchSkin" class="col-md-2 control-label">전체 검색 스킨설정</label>
                        <div class="col-md-5">
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
            </div>
        </div>
    </form>
</section>
</div>
@endsection
