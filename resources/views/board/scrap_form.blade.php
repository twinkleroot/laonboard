<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>스크랩하기 | {{ Cache::get("config.homepage")->title }}</title>
<!-- css -->
<link rel="stylesheet" type="text/css" href="{{ ver_asset('bootstrap/css/bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('font-awesome/css/font-awesome.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/common.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('css/scrap.css') }}">
<!-- js -->
<script src="{{ ver_asset('js/jquery-3.1.1.min.js') }}"></script>
<script src="{{ ver_asset('js/common.js') }}"></script>
<script>
    $(document).ready(function(){
        if(window.name != 'winScrap') {
            alert('올바른 방법으로 사용해 주십시오.');
            window.close();
        }
    });
</script>
</head>
<body class="popup">
<form method="post" action="{{ route('scrap.store')}}">
<input type="hidden" name="boardName" value="{{ $boardName }}" />
<input type="hidden" name="boardId" value="{{ $boardId }}" />
<input type="hidden" name="writeId" value="{{ $write->id }}" />
{{ csrf_field()}}

    <div id="header" class="popup">
    <div class="container">
        <div class="title">
            <span>스크랩하기</span>
        </div>

        <div class="cbtn">
            <input type="submit" class="btn btn-sir" value="스크랩 하기" />
        </div>
    </div>
    </div>

    <div id="bd_scrap" class="container">
        <p><strong>제목 확인 및 댓글 쓰기</strong></p>
        <div class="help bg-info mb15">
            스크랩을 하시면서 감사 혹은 격려의 댓글을 남기실 수 있습니다.
        </div>
        <table class="table box">
            <tbody>
                <tr>
                    <td class="popin">
                        제목
                    </td>
                    <td class="text-left">
                        {{ $write->subject }}
                    </td>
                </tr>
                <tr>
                    <td class="popin">
                        댓글
                    </td>
                    <td class="text-left">
                        <textarea class="form-control" name="content" rows="4"></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</form>

</body>
</html>
