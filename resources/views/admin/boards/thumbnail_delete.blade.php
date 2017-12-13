@extends('admin.layouts.basic')

@section('title'){{ $board->subject }} 게시판 썸네일 삭제 | {{ cache("config.homepage")->title }}@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>{{ $board->subject }} 게시판 썸네일 삭제</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">게시판 관리</li>
            <li class="depth">{{ $board->subject }} 게시판 썸네일 삭제</li>
        </ul>
    </div>
</div>
@if(notNullCount($files) == 0)
    <div id="body_tab_type2">
        <span class="txt">삭제할 썸네일파일이 없습니다.</span>
    </div>
@else
<div id="body_tab_type2">
    <span class="txt">완료 메세지가 나오기 전에 프로그램의 실행을 중지하지 마십시오.</span>
</div>
<div class="body-contents">
    <ul class="file_delete">
        @php
            $count = 0;
            foreach($files as $file) {
                $count++;
                File::delete($file);
                echo "<li>$file</li>";

                if($count % 10 == 0) {
                    echo "<br />";
                }
            }
        @endphp
        <li>완료됨</li>
    </ul>

    <div class="file_delete_txt">
        <p><span class="success">썸네일 {{ $count }}건의 삭제 완료됐습니다.</span><br>
        프로그램의 실행을 끝마치셔도 좋습니다.<p>
    </div>
</div>
@endif
<div class="body-contents">
    <a class="btn btn-sir" href="{{ route('admin.boards.edit', $board->table_name). "?$queryString" }}">게시판 수정으로 돌아가기</a>
</div>
<script>
    var menuVal = 300100;
</script>
@endsection
