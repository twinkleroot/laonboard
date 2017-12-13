@extends('admin.layouts.basic')

@section('title')모듈 관리 | {{ cache('config.homepage')->title }}@stop

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>설치된 모듈</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">모듈 관리</li>
            <li class="depth">설치된 모듈</li>
        </ul>
    </div>
</div>
<div id="body_tab_type2">
    <span class="txt">
        <a href="{{ route('admin.modules.index') }}">전체 모듈</a>
        <span class="count">{{ Module::count() }}</span>
        <a href="{{ route('admin.modules.index'). "?use=yes". ($keyword ? "&keyword=". $keyword : '') }}">사용중</a>
        <span class="count use">{{ notNullCount(Module::enabled()) }}</span>
        <a href="{{ route('admin.modules.index'). "?use=no". ($keyword ? "&keyword=". $keyword : '') }}">미사용중</a>
        <span class="count nonuse">{{ notNullCount(Module::disabled()) }}</span>
    </span>
    <div class="submit_btn">
        {{-- <a class="btn btn-default" href="{{ route('admin.modules.create') }}" role="button">모듈 추가하기</a> --}}
    </div>
</div>
<div class="body-contents">
    <ul id="adm_btn">
        <li><input type="button" id="use" class="btn btn-sir" value="사용" onclick="submitModuleList(this.id)"></li>
        <li><input type="button" id="unuse" class="btn btn-sir" value="사용 중지" onclick="submitModuleList(this.id)"></li>
        {{-- <li><input type="submit" id="update" class="btn btn-sir" value="업데이트" onclick="submitModuleList(this.id)"></li> --}}
        <li><input type="button" id="delete" class="btn btn-sir" value="삭제" onclick="submitModuleList(this.id)"></li>
    </ul>
    <div id="adm_sch">
        <form role="form" method="GET" action="{{ route('admin.modules.index') }}">
            @if(Request::filled('use'))
                <input type="hidden" name="use" value="{{ Request::get('use') }}">
            @endif
            <label for="keyword" class="sr-only">검색어</label>
            <input type="text" name="keyword" @if($keyword != '') value="{{ $keyword }}" @endif class="search">
            <button type="submit" id="" class="btn search-icon">
                <i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">검색</span>
            </button>
        </form>
    </div>
    <div id="module">
        <form name="moduleList" id="moduleList" class="form-horizontal" onsubmit="return moduleListSubmit(this);" method="post">
            <input type="hidden" id="type" name="type" value="">
            {{ csrf_field() }}
            {{ method_field('POST') }}
            <table class="table table-striped box">
                <thead>
                    <th class="td_chk">
                        <input type="checkbox" name="chkAll" onclick="checkAll(this.form)"/>
                    </th>
                    <th>모듈</th>
                    <th class="td_mngsmall">설정</th>
                </thead>
                <tbody>
                @forelse($modules as $module)
                    <tr>
                        <td class="td_chk">
                            @if($module->enabled())
                            <div class="use"><span class="sr-only">사용중</span></div>
                            @endif
                            <input type="checkbox" name="chkId[]" class="moduleName" value='{{ $module->getLowerName() }}' />
                        </td>
                        <td class="td_module">
                            <span class="title"><a href="{{ route('admin.modules.show', $module->getLowerName()) }}">{{ $module->getName() }}</a></span>
                            <span class="info">{{ $module->getDescription() }}</span>
                            <span class="ver">버전 {{ $module->getVersion() }}</span>
                            <span class="maker"><a href="{{ $module->getLink() ? : '' }}" target="_blank">{{ $module->getAuthor() }}</a> 제작</span>
                            {{-- <div class="update">댓글의 새로운 업데이트가 있습니다. <a href="#" class="udgo">업데이트 내역 확인</a>또는<a href="#" class="udgo">업데이트</a></div> --}}
                        </td>
                        <td class="td_link">
                            <a href="{{ route('admin.modules.show', $module->getLowerName()) }}">상세보기</a>
                            @if($module->getAdminLink() && Route::has($module->getAdminLink()))
                            <a href="{{ route($module->getAdminLink()) }}">설정</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">
                            <span class="empty_table">
                                <i class="fa fa-exclamation-triangle"></i> 설치된 모듈이 없습니다.
                            </span>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </form>
    </div>
</div>
<script>
var menuVal = 400100;

function submitModuleList(id)
{
    $("#type").val(id);
    $("#moduleList").submit();
}

function moduleListSubmit(f)
{
    var type = $("#type").val();
    var selectNames = selectIdsByCheckBox(".moduleName");
    if(selectNames.length == 0) {
        alert(document.getElementById(type).value + '할 모듈을 한 개 이상 선택하세요.')
        return false;
    }
    if(type == "use") {
        f.action = '/admin/modules/active';
    }
    if(type == "unuse") {
        f.action = '/admin/modules/inactive';
    }
    // if(type == "update") {
    //     f.action = '/admin/modules/update';
    // }
    if(type == "delete") {
        if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다\n\n답변글이 있는 게시글을 선택하신 경우\n답변글도 선택하셔야 게시글이 삭제됩니다.")) {
            return false;
        }
        f._method.value = 'DELETE';
        f.action = '/admin/modules';
    }
    return true;
}
</script>
@stop
