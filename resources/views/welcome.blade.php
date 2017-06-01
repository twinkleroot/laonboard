@extends('theme')

@section('title')
    환영합니다. | {{ Cache::get("config.homepage")->title }} 
@endsection

@section('content')
@if(Session::has('message'))
  <div class="alert alert-info">
    {{Session::get('message') }}
  </div>
@endif
@endsection
