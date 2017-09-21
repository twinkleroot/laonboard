<html>
<head>
<script src="{{ ver_asset('js/jquery-3.1.1.min.js') }}"></script>
<script>
    $(document).ready(function(){
        @if($message == '1044' || $message == '1045')
        alert("데이터 베이스 연결정보를 확인해 주세요.")
        @else
        alert("쿼리 요청 정보가 올바르지 않습니다.");
        @endif
        history.back();
    });
</script>
</head>
<body>
</body>
</html>
