<html>
<head>
    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script>
        $(document).ready(function(){
            if(confirm("{{ $confirm }}")) {
                location.href = "{{ route('scrap.index')}}";
            } else {
                window.close();
            }
        });
    </script>
</head>
</html>
