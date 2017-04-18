@extends('theme')

@section('title')
    관리자 모드 | {{ $config->title }}
@endsection

@section('content')
<div class="container">
    <div>
        <ul>
            <li><a href="{{ route('admin.config') }}">환경 설정</a></li>
            <li><a href="{{ route('admin.users.index') }}">회원 관리</a></li>
            <li><a href="{{ route('admin.groups.index') }}">게시판 그룹 관리</a></li>
            <li><a href="{{ route('admin.boards.index') }}">게시판 관리</a></li>
            <li><a href="{{ route('admin.points.index') }}">포인트 관리</a></li>
            <li><a href="{{ route('admin.menus.index') }}">메뉴 설정</a></li>
            <li><a href="{{ route('menuTest') }}">메뉴 테스트</a></li>
        </ul>
    </div>
    <div>
        <p>
            메인 페이지 - 신규가입회원 목록, 최근 게시물, 최근 포인트 발생내역 위치할 예정
        </p>
    </div>
</div>
@endsection
