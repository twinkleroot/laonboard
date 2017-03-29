@extends('theme')

@section('title')
    LaBoard | 게시판
@endsection

@section('content')
@if(Session::has('message'))
    <div class="alert alert-info">
    {{Session::get('message') }}
    </div>
@endif
게시판
@endsection
