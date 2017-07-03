<html>
<head>
    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script>
        $(document).ready(function(){
            alert("{{ $exception->getMessage() }}");
            history.back();
        });
    </script>
</head>
<body>
    {{-- <button type="button" onclick="history.back();">이전페이지로</button> --}}
    {{-- <a href="{{ route('home') }}">홈페이지 메인</a> --}}
</body>
</html>
