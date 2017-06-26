@extends('admin.admin')

@section('title')
    관리자 모드 | {{ $config->title }}
@endsection

@section('content')
<p>
    관리자 메인 페이지 - 신규가입회원 목록, 최근 게시물, 최근 포인트 발생내역 위치할 예정
</p>
@endsection
<script>
    var menuVal = 0;
</script>
