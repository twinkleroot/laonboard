<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>게시물 {{ $type=='move' ? '이동' : '복사' }} | {{ Cache::get("config.homepage")->title }}</title>
<!-- css -->
<link rel="stylesheet" type="text/css" href="{{ ver_asset('bootstrap/css/bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('font-awesome/css/font-awesome.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/common.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('css/move.css') }}">
<!-- Scripts -->
<script>
    window.Laravel = {!! json_encode([
        'csrfToken' => csrf_token(),
    ]) !!};
</script>
<script src="{{ ver_asset('js/jquery-3.1.1.min.js') }}"></script>
<script src="{{ ver_asset('js/common.js') }}"></script>
</head>
<body class="popup">
<form role="form" method="POST" action="" onsubmit="return formMoveListSubmit(this);">
    <input type="hidden" name="type" value="{{ $type }}" />
    {{ csrf_field() }}
    <div id="header" class="popup">
        <div class="container">
            <div class="title">
                <span>게시물 {{ $type=='move' ? '이동' : '복사' }}</span>
            </div>
            <div class="cbtn">
                <input type="submit" class="btn btn-sir" value="{{ $type=='move' ? '이동' : '복사' }}" onclick="document.pressed=this.value"/>
                <input type="button" class="btn btn-default" onclick="window.close();" value="창닫기"/>
            </div>
        </div>
    </div>

    <div id="bd_move" class="container">
        <div class="form-horizontal">
            <table class="table box">
                <thead>
                    <tr>
                        <th><input type="checkbox" name="chkAll" onclick="checkAll(this.form)"></th>
                        <th>게시판</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($boards as $board)
                    <tr @if($board->id == $currentBoard->id) class="on" @endif>
                        <td class="bd_check">
                            <input type="checkbox" name="chkId[]" class="boardId" value='{{ $board->id }}'>
                        </td>
                        <td>
                            <label for="chkId[]">
                                <span class="gr1">{{ $board->group->subject }}</span> <!-- 그룹의 첫번째는 gr1 -->
                                <i class="fa fa-angle-right"></i>
                                <span>{{ $board->subject }} ({{ $board->table_name }})</span>
                            </label>
                            @if($board->id == $currentBoard->id)
                                <span class="here">현재</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</form>
<script>
// 복사 및 이동 폼 서브밋 전 실행되는 함수
function formMoveListSubmit(f) {
    var selected_id_array = selectIdsByCheckBox(".boardId");

    if(selected_id_array.length == 0) {
        alert('게시물을 ' + document.pressed + '할 게시판을 한 개 이상 선택하세요.')
        return false;
    }

    f.action = '{{ route('board.update.move', $currentBoard->table_name) }}';

    return true;
}
</script>
</body>
