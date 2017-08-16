@extends('admin.admin')

@section('title')
    내용 관리 | {{ Cache::get('config.homepage')->title }}
@endsection

@section('include_script')
    <script src="{{ asset('js/common.js') }}"></script>
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
        <a class="btn btn-default" href="{{ route('admin.contents.create')}}" role="button">내용 추가</a>
    </div>
</div>
<div class="body-contents">
    @if(Session::has('message'))
        <div class="alert alert-info">
            {{ Session::get('message') }}
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
            @if($contents->total() > 0)
            @foreach ($contents as $content)
                <tr>
                    <td class="td_id">{{ $content->content_id }}</td>
                    <td class="td_subject">{{ $content->subject }}</td>
                    <td class="td_mngsmall">
                        <a href="{{ route('admin.contents.edit', $content->content_id) }}">수정</a>
                        <a href="{{ route('contents.show', $content->content_id) }}">보기</a>
                        <a href="{{ route('admin.contents.destroy', $content->id) }}" onclick="del(this.href); return false;">삭제</a>
                    </td>
                </tr>
            @endforeach
            @else
                <tr>
                    <td colspan="3" class="empty_table">
                        자료가 한건도 없습니다.
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
    {{ $contents->links() }}
</div>
@endsection
