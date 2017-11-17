@extends('admin.layouts.basic')

@section('title')내용 관리 | {{ Cache::get('config.homepage')->title }}@stop

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
<script>
    var menuVal = 400100;
</script>
@stop

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>내용 관리</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">모듈 관리</li>
            <li class="depth">설치된 모듈</li>
            <li class="depth">내용 관리</li>
        </ul>
    </div>
</div>
<div id="body_tab_type2">
    <span class="txt">전체 내용 {{ $contents->total() }}건</span>
    <div class="submit_btn">
        @unless(isDemo())
        <a class="btn btn-sir" href="{{ route('admin.content.create')}}" role="button">내용추가</a>
        <a class="btn btn-default" href="{{ route('admin.modules.index') }}">모듈목록</a>
        @endunless
    </div>
</div>
<div class="body-contents">
@if(Session::has('message'))
    <div id="adm_save">
        <span class="adm_save_txt">{{ Session::get('message') }}</span>
        <button onclick="alertclose()" class="adm_alert_close">
            <i class="fa fa-times"></i>
        </button>
    </div>
@endif
    <div id="mb" class="">
        <table class="table table-striped box">
            <thead>
                <th>ID</th>
                <th>제목</th>
                <th>하단 노출</th>
                <th>관리</th>
            </thead>
            <tbody>
            @forelse ($contents as $content)
                <tr>
                    <td class="td_id">{{ $content->content_id }}</td>
                    <td class="td_subject">{{ $content->subject }}</td>
                    <td class="td_mngsmall">{{ $content->show == 1 ? '노출' : '숨김'}}</td>
                    <td class="td_mngsmall">
                        <a href="{{ route('admin.content.edit', $content->content_id) }}">수정</a>
                        <a href="{{ route('content.show', $content->content_id) }}">보기</a>
                        <a href="{{ route('admin.content.destroy', $content->content_id) }}" onclick="delPost('deleteForm{{ $content->id }}')">
                            삭제
                        </a>
                        <form id="deleteForm{{ $content->id }}" action="{{ route('admin.content.destroy', $content->content_id) }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">
                        <span class="empty_table">
                            <i class="fa fa-exclamation-triangle"></i> 자료가 없습니다.
                        </span>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $contents->links() }}
</div>
@stop
