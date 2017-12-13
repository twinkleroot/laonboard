@extends('admin.layouts.basic')

@section('title')세션 일괄삭제 | {{ cache("config.homepage")->title }}@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>세션파일 일괄삭제</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">환경 설정</li>
            <li class="depth">세션파일 일괄삭제</li>
        </ul>
    </div>
</div>
@if(notNullCount($sessions) == 0)
    <div id="body_tab_type2">
        <span class="txt">삭제할 세션이 없습니다.</span>
    </div>
@else
<div id="body_tab_type2">
    <span class="txt">완료 메세지가 나오기 전에 프로그램의 실행을 중지하지 마십시오.</span>
</div>
<div class="body-contents">
    <ul class="file_delete">
        <?php
            $count = 0;
            foreach($sessions as $session) {
                $count++;
                session()->forget($session);
                echo "<li>$session</li>";

                if($count % 10 == 0) {
                    echo "<br />";
                }
            }
        ?>
        <li>완료됨</li>
    </ul>

    <div class="file_delete_txt">
        <p><span class="success">세션데이터 {{ $count }}건 삭제 완료됐습니다.</span><br>
        프로그램의 실행을 끝마치셔도 좋습니다.<p>
    </div>
</div>
@endif

<script>
    var menuVal = 100700;
</script>
@endsection
