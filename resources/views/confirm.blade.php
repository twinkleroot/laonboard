<html>
<head>
    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script>
        $(document).ready(function(){
            var redirect = "{{ isset($redirect) ? $redirect : '' }}";
            if(confirm("{!! $message !!}")) {
                location.href = redirect;
            } else {
                window.close();
            }
        });
    </script>
</head>
</html>
