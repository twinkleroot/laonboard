<html>
<head>
<script src="{{ ver_asset('js/jquery-3.1.1.min.js') }}"></script>
<script>
    $(document).ready(function(){
        alert("{!! $message !!}");

        var popup = "{{ $popup or 0 }}";
        var reload = "{{ $reload or 0 }}";
        var redirect = "{{ $redirect or '' }}";
        var openerRedirect = "{{ $openerRedirect or '' }}";

        // 팝업창에서 처리하는 경우
        if(reload == 1) {
            opener.location.reload();   // 팝업을 띄운 페이지를 reload
        }

        if(openerRedirect != '') {
            opener.location.href = openerRedirect;    // 팝업을 띄운 페이지 redirect
        }

        if(popup == 1) {
            window.close();             // 팝업창을 닫는다.
        } else {
            if(opener) {
                window.close();             // 팝업창을 닫는다.
            }
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
