<script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
<script>
$(function() {
    var $opener = window.opener;

    $opener.$("input[name=certType]").val("{{ $certType }}");
    $opener.$("input[name=name]").val("{{ $name }}").attr("readonly", true);
    $opener.$("input[name=hp]").val("{{ $hp }}").attr("readonly", true);
    $opener.$("input[name=certNo]").val("{{ $certNo }}");

    alert("본인의 휴대폰번호로 확인 되었습니다.");
    window.close();
});
</script>
