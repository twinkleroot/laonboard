<html>
<head>
    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script>
        $(document).ready(function(){
            alert("{{ $message }}");

            var popup = {{ isset($popup) ? $popup : 0 }};
            var reload = {{ isset($reload) ? $reload : 0 }};
            var redirect = '{{ isset($redirect) ? $redirect : '' }}';

            // 팝업창에서 처리하는 경우
            if(reload == 1) {
                opener.location.reload();   // 팝업을 띄운 페이지를 reload
            }
            if(popup == 1) {
                window.close();             // 팝업창을 닫는다.
            }

            if(redirect != '') {
                location.href = redirect;
            } else {
                history.back();
            }
        });
    </script>
</head>
</html>