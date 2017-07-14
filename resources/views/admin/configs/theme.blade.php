@extends('admin.admin')

@section('title')
    테마 설정 | {{ cache('config.homepage')->title }}
@endsection

@section('include_script')
    <script src="{{ asset('js/common.js') }}"></script>
    <script>
        var menuVal = 100300
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
</div>
<div id="body_tab_type2">
    <span class="txt">설치된 테마 : 1</span>
</div>
<div class="body-contents">
    <ul class="theme_list">
        <li class="themebox">
            <div class="tmli_if">
                <span class="img"><img src="http://scontent.cdninstagram.com/t51.2885-15/s480x480/e35/12145610_1672253046395088_1360158264_n.jpg?ig_cache_key=MTExMzUzNDM4OTMxMDM4Mzc3NQ%3D%3D.2"></span>
                <span class="txt">스킨명</span>
            </div>
            <div class="tmli_btn">
                <span class="theme_sl use">사용중</span>
                <button class="theme_sl use_cancel">사용안함</button>
                <a href="#" class="theme_pr">미리보기</a>
                <button class="theme_preview">상세보기</button>
            </div>
        </li>


        <li class="themebox">
            <div class="tmli_if">
                <span class="img"><img src="http://scontent.cdninstagram.com/t51.2885-15/s480x480/e35/12145610_1672253046395088_1360158264_n.jpg?ig_cache_key=MTExMzUzNDM4OTMxMDM4Mzc3NQ%3D%3D.2"></span>
                <span class="txt">스킨명</span>
            </div>
            <div class="tmli_btn">
                <button class="theme_sl use_apply">테마적용</button>
                <a href="#" class="theme_pr">미리보기</a>
                <button class="theme_preview">상세보기</button>
            </div>
        </li>
    </ul>
</div>
@endsection
