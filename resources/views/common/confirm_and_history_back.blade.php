<html>
<head>
<script src="{{ ver_asset('js/jquery-3.1.1.min.js') }}"></script>
<script>
    $(document).ready(function(){
        if(confirm("{{ $confirm }}")) {
            history.back();
        } else {
            var redirect = "{{ $redirect or '' }}";
            location.href = redirect;
        }
    });
</script>
</head>
</html>
