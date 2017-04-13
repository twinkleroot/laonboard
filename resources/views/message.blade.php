<html>
<head>
    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script>
        $(document).ready(function(){
            alert("{{ $message }}");
            history.back();
        });
    </script>
</head>
</html>
