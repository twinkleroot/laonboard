@extends('theme')

@section('title')
    게시판 | LaBoard
@endsection

@section('content')
@if(Session::has('message'))
    <div class="alert alert-info">
    {{Session::get('message') }}
    </div>
@endif
게시판
@endsection
