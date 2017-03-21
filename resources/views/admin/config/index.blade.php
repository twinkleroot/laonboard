@extends('theme')

@section('title')
    LaBoard | 환경 설정
@endsection

@section('content')
@if(Session::has('message'))
  <div class="alert alert-info">
    {{Session::get('message') }}
  </div>
@endif
    환경 설정 페이지
@endsection
