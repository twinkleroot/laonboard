<html>
<head>
<script src="{{ ver_asset('js/jquery-3.1.1.min.js') }}"></script>
<script>
    $(document).ready(function(){
        alert("{{ $exception->getMessage() }}");
        history.back();
    });
</script>
</head>
<body>
</body>
</html>
