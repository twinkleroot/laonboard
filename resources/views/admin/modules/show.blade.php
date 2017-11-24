@extends('admin.layouts.basic')

@section('title')모듈 상세보기 | {{ cache('config.homepage')->title }}@stop

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>설치된 모듈</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">모듈 관리</li>
            <li class="depth">모듈 상세보기</li>
        </ul>
    </div>
</div>
<div id="body_tab_type2">
    <ul>
        <li class="tab"><a href="#module_info">요약</a></li>
        <li class="tab"><a href="#module_update">모듈정보</a></li>
    </ul>
    <div class="submit_btn">
        <a class="btn btn-default" href="{{ route('admin.modules.index') }}">모듈목록</a>
    </div>
</div>
<div class="body-contents">
    <div id="module">
        <div id="module_info">
            <div class="image">
                @if($module->getScreenshotName())
                <img src="{{ ver_asset('modules/'. $module->getLowerName(). '/img/'. $module->getScreenshotName()) }}">
                @endif
            </div>
            <div class="info">
                <form name="moduleForm" id="moduleForm" action="" class="form-horizontal" method="post">
                    <input type="hidden" name="moduleName" value="{{ $module->getName() }}">
                    {{ csrf_field() }}
                    {{ method_field('post') }}
                    <h1>{{ $module->getName() }}</h1>
                    <ul>
                        <li>버전: {{ $module->getVersion() }}</li>
                        <li>
                            제작: {{ $module->getAuthor() }}
                            <a href="{{ $module->getLink() }}" class="link" title="연결된 링크로 이동" target="_blank">연결된 링크로 이동</a>
                        </li>
                        <li class="module_btn">
                        @if($module->enabled())
                            <a onclick="moduleFormSubmit('unuse');" class="btn btn-danger">사용중지</a>
                        @else
                            <a onclick="moduleFormSubmit('use');" class="btn btn-sir">사용</a>
                        @endif
                            {{-- <a onclick="moduleFormSubmit('update');" class="btn btn-sir">업데이트</a> --}}
                            <a onclick="moduleFormSubmit('delete');" class="btn btn-sir">삭제</a>
                            @if($module->getAdminLink() && Route::has($module->getAdminLink(). ".index"))
                            <a href="{{ route($module->getAdminLink(). ".index") }}" class="btn btn-sir">설정</a>
                            @endif
                        </li>
                    </ul>
                </form>
            </div>
        </div>
        <div id="module_update" style="height: 500px;">
            {{ $module->getDetail() ? : $module->getDescription() }}
        </div>
    </div>
</div>
<script>
var menuVal = 400100;

function moduleFormSubmit(type)
{
    var f = $("#moduleForm");
    if(type == "use") {
        f.attr('action', '{{ route('admin.modules.active') }}');
    }
    if(type == "unuse") {
        f.attr('action', '{{ route('admin.modules.inactive') }}');
    }
    // if(type == "update") {
    {{--      f.attr('action', '{{ route('admin.modules.update') }}'); --}}
    // }
    if(type == "delete") {
        if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다\n\n답변글이 있는 게시글을 선택하신 경우\n답변글도 선택하셔야 게시글이 삭제됩니다.")) {
            return false;
        }
        f.attr('action', '{{ route('admin.modules.destroy') }}');
        $("#moduleForm input[name=_method]").val('delete');
    }
    f.submit();
}
</script>
@stop
