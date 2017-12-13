@extends('admin.layouts.basic')

@section('title')메일 테스트 | {{ Cache::get("config.homepage")->title }}@endsection

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
<script>
    var menuVal = 100500;
</script>
@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>메일 테스트</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">환경 설정</li>
            <li class="depth">메일 테스트</li>
        </ul>
    </div>
</div>

<div class="body-contents">
@if ($errors->any())
    <div id="adm_save">
        <span class="adm_save_txt">{{ $errors->first() }}</span>
        <button onclick="alertclose()" class="adm_alert_close">
            <i class="fa fa-times"></i>
        </button>
    </div>
@else
    @if(Session::has('successAddress') && notNullCount(Session::get('successAddress')) > 0)
    <div id="adm_save">
        <span class="adm_save_txt">다음 {{ notNullCount(Session::get('successAddress')) }}개의 메일 주소로 테스트 메일 발송이 완료되었습니다.</span>
        <button onclick="alertclose()" class="adm_alert_close">
            <i class="fa fa-times"></i>
        </button>
    </div>
    <ul>
        @foreach(Session::get('successAddress') as $email)
        <li>
            {{ $email }}
        </li>
        @endforeach
    </ul>
    해당 주소로 테스트 메일이 도착했는지 확인해 주십시오.<br />
    만약, 테스트 메일이 오지 않는다면 더 다양한 계정의 메일 주소로 메일을 보내 보십시오.<br />
    그래도 메일이 하나도 도착하지 않는다면 메일 서버(sendmail server)의 오류일 가능성이 높으니, 웹 서버관리자에게 문의하여 주십시오.<br />
    @elseif(Session::has('successAddress'))
    <div id="adm_save">
        <span class="adm_save_txt">테스트 메일 발송에 실패하였습니다.</span>
        <button onclick="alertclose()" class="adm_alert_close">
            <i class="fa fa-times"></i>
        </button>
    </div>
    @endif
@endif

메일서버가 정상적으로 동작 중인지 확인할 수 있습니다.<br />
아래 입력칸에 테스트 메일을 발송하실 메일 주소를 입력하시면, [메일검사] 라는 제목으로 테스트 메일을 발송합니다.<br /><br />
<div class="row">
<div class="col-md-6">
    <form method="post" action="{{ route('admin.email.send') }}">
    {{ csrf_field() }}
        <div id="mail" class="panel panel-default">

            <div class="panel-heading bg-sir">
                받는 메일주소
            </div>

            <div class="panel-body row">
                <div class="col-md-8">
                    <input type="text" class="form-control required" name="email" id="email" value="{{ Cache::get("config.homepage")->superAdmin }}" />
                </div>
                <input type="submit" class="btn btn-sir" value="발송" />
            </div>

        </div>
    </form>
</div>
</div>

만약 [메일검사] 라는 내용으로 테스트 메일이 도착하지 않는다면 보내는 메일서버 혹은 받는 메일서버 중 문제가 발생했을 가능성이 있습니다.<br />
따라서 보다 정확한 테스트를 원하신다면 여러 곳으로 테스트 메일을 발송하시기 바랍니다.
</div>
@endsection
