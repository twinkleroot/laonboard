@extends('layout.'. cache('config.skin')->layout. '.basic')

@section('title')
    메인 | {{ cache("config.homepage")->title }}
@endsection

@section('include_css')
<!-- 최근게시물용 CSS파일 -->
<link rel="stylesheet" type="text/css" href="{{ asset('themes/'.$skin.'/css/latest.css') }}">
<!-- 팝업레이어용 CSS파일 -->
<link rel="stylesheet" type="text/css" href="{{ asset('css/popuplayer.css') }}">
@endsection

{{-- 팝업 레이어 --}}
@section('popup')
    @include('board.popup')
@endsection

@section('content')
    <div class="mainVisual">
        <div class="txt">

        </div>
    </div>
    <div class="mainCover">
        <div class="cover">
            <a href="http://demo.laonboard.com/login">
                <div class="box">
                    <span class="icon">
                        <i class="fa fa-desktop"></i>
                    </span>
                    <p class="head">데모 체험</p>
                    <p class="body">라라벨 보드의 놀라움을<br>직접 체험해 보세요</p>
                </div>
            </a>
        </div>
        <div class="cover">
            <a href="{{ route('board.index', 'download') }}">
                <div class="box">
                    <span class="icon">
                        <i class="fa fa-download"></i>
                    </span>
                    <p class="head">다운로드</p>
                    <p class="body">라라벨 보드를 이용해<br>홈페이지를 만들어 보세요</p>
                </div>
            </a>
        </div>
        <div class="cover">
            <a href="{{ route('board.index', 'qna') }}">
                <div class="box">
                    <span class="icon">
                        <i class="fa fa-question"></i>
                    </span>
                    <p class="head">묻고 답하기</p>
                    <p class="body">혼자 하기 어려우시다구요?<br>회원들과 함께 해결해 보세요</p>
                </div>
            </a>
        </div>
    </div>
    <!--<div class="mainContents">
        컨텐츠
    </div>-->
    <div class="contents row">
        {{-- 최근 게시물 리스트--}}
        @include("latest.$skin.index")
    </div>
@endsection
