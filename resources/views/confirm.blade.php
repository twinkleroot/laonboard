<html>
<head>
<script src="{{ ver_asset('js/jquery-3.1.1.min.js') }}"></script>
<script>
    $(document).ready(function(){
        var redirect = "{{ $redirect or '' }}";
        if(confirm("{!! $message !!}")) {
            location.href = redirect;
        } else {
            window.close();
        }
    });
</script>
</head>
</html>
