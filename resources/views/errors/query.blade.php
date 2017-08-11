<html>
<head>
    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script>
        $(document).ready(function(){
            @if($message == '1044')
            alert("데이터 베이스 연결에 실패하였습니다.\n관리자에게 문의해 주세요.")
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
