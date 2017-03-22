@extends('theme')

@section('title')
    LaBoard | 환영합니다.
@endsection

@section('content')
@if(Session::has('message'))
  <div class="alert alert-info">
    {{Session::get('message') }}
  </div>
@endif
@endsection
