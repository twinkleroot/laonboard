<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>게시물 {{ $type=='move' ? '이동' : '복사' }} | {{ \App\Config::getConfig('config.homepage')->title }}</title>

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>

    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>

</head>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">게시물 {{ $type=='move' ? '이동' : '복사' }}</div>
            <form class="form-horizontal" role="form" method="POST" action="" onsubmit="return formMoveListSubmit(this);">
                <input type="hidden" name="type" value="{{ $type }}" />
                {{ csrf_field() }}
                <div class="panel-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th> <!-- 전체선택 -->
                					<input type="checkbox" name="chkAll" onclick="checkAll(this.form)">
                				</th>
                                <th>게시판</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($boards as $board)
                            <tr>
                                <td class="bd_check"><input type="checkbox" name="chk_id[]" class="boardId" value='{{ $board->id }}'></td>
                                <td>
                                    {{ $board->group->subject . ' > ' . $board->subject . ' (' . $board->table_name . ')' }}
                                    @if($board->id == $currentBoard->id) 현재 @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="panel-heading">
                    <input type="submit" class="btn btn-primary" value="{{ $type=='move' ? '이동' : '복사' }}" onclick="document.pressed=this.value"/>
                    <input type="button" class="btn btn-primary" onclick="window.close();" value="창닫기"/>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
$(function(){

});

// 모두 선택
function checkAll(form) {
    var chk = document.getElementsByName("chk_id[]");

    for (i=0; i<chk.length; i++) {
        chk[i].checked = form.chkAll.checked;
    }
}

// 복사 및 이동 폼 서브밋 전 실행되는 함수
function formMoveListSubmit(f) {
    var selected_id_array = selectIdsByCheckBox(".boardId");

    if(selected_id_array.length == 0) {
        alert('게시물을 ' + document.pressed + '할 게시판을 한 개 이상 선택하세요.')
        return false;
    }

    f.action = '{{ route('board.moveUpdate', $currentBoard->id) }}';

    return true;
}

// 선택한 항목들 id값 배열에 담기
function selectIdsByCheckBox(className) {
    var send_array = Array();
    var send_cnt = 0;
    var chkbox = $(className);

    for(i=0; i<chkbox.length; i++) {
        if(chkbox[i].checked == true) {
            send_array[send_cnt] = chkbox[i].value;
            send_cnt++;
        }
    }

    return send_array;
}
</script>
