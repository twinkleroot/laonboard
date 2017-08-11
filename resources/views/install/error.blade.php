@extends('install.layout')

@section('title')
    {{ config('app.name')." 설치" }}
@endsection

@section('step')
    INSTALLATION
@endsection

@section('content')

@foreach($results as $key => $value)
<div class="ins_inner">
    <p>
        {{ $key }} 디렉토리의 퍼미션을 707로 변경하여 주십시오.<br />
        $ chmod -R 707 {{ $key }}<br />
        위 명령 실행후 브라우저를 새로고침 하십시오.
    </p>
</div>
@endforeach
@endsection
