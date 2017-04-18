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
    {{ $board->table_name }} 게시판 리스트
@endsection
