<html>
<head>
    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script>
        $(document).ready(function(){
            alert("{{ $message }}");
            location.replace("{{ route('index') }}");
        });
    </script>
</head>
</html>
