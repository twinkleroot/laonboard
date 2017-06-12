@extends('admin.admin')

@section('title')
    내용 관리 | {{ Cache::get('config.homepage')->title }}
@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>내용관리</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">내용관리</li>
            <li class="depth">내용목록</li>
        </ul>
    </div>
    <div class="pull-right">
        <ul class="mb_btn" style="margin-top:8px;">
            <li>
                <a class="btn btn-default" href="{{ route('contents.create')}}" role="button">내용 추가</a>
            </li>
        </ul>
    </div>
</div>
<div class="body-contents">
    @if(Session::has('message'))
        <div class="alert alert-info">
            {{ Session::get('message') }}
        </div>
    @endif

    <div id="mb" class="">
        <ul class="mb_btn mb10 pull-left">
            <li>
                <span class="total">전체 내용 {{ $contents->total() }}건</span>
            </li>
        </ul>

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
                    <td class="text-left">
                        {{ $content->content_id }}
                    </td>
                    <td class="text-left">{{ $content->subject }}</td>
                    <td>
                        <a href="{{ route('contents.edit', $content->content_id) }}">수정</a>
                        <a href="{{ route('contents.show', $content->content_id) }}">보기</a>
                        <a href="{{ route('contents.destroy', $content->id) }}" onclick="deleteConfirm()">삭제</a>
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
<script>
function deleteConfirm() {
    if(confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?")) {
        return true;
    } else {
        return false;
    }
}
</script>
@endsection
