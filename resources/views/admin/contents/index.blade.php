@extends('admin.admin')

@section('title')
    내용 관리 | {{ Cache::get('config.homepage')->title }}
@endsection

@section('include_script')
    <script src="{{ ver_asset('js/common.js') }}"></script>
    <script>
        var menuVal = 300400
    </script>
@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>내용관리목록</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">내용관리</li>
            <li class="depth">내용목록</li>
        </ul>
    </div>
</div>
<div id="body_tab_type2">
    <span class="txt">전체 내용 {{ $contents->total() }}건</span>
    <div class="submit_btn">
        @unless(isDemo())
        <a class="btn btn-default" href="{{ route('admin.contents.create')}}" role="button">내용 추가</a>
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
                <th>관리</th>
            </thead>
            <tbody>
            @forelse ($contents as $content)
                <tr>
                    <td class="td_id">{{ $content->content_id }}</td>
                    <td class="td_subject">{{ $content->subject }}</td>
                    <td class="td_mngsmall">
                        <a href="{{ route('admin.contents.edit', $content->content_id) }}">수정</a>
                        <a href="{{ route('content.show', $content->content_id) }}">보기</a>
                        <a href="{{ route('admin.contents.destroy', $content->content_id) }}" onclick="delPost('deleteForm{{ $content->id }}')">
                            삭제
                        </a>
                        <form id="deleteForm{{ $content->id }}" action="{{ route('admin.contents.destroy', $content->content_id) }}" method="POST" style="display: none;">
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
@endsection
