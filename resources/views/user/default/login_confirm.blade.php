<html>
<head>
    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script>
        $(document).ready(function(){
            if(confirm("{{ $confirm }}")) {
                history.back();
            } else {
                var redirect = "{{ isset($redirect) ? $redirect : '' }}";
                location.href = redirect;
            }
        });
    </script>
</head>
</html>
