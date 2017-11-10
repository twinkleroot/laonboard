<script>
function request(){
    document.form1.action = "{{ $commonSvlUrl }}";
    document.form1.method = "post";

    document.form1.submit();
}
</script>

<form name="form1">
<!-- 인증 요청 정보 -->
<!--// 필수 항목 -->
<input type="hidden" name="tc" value="kcb.oknm.online.safehscert.popup.cmd.P901_CertChoiceCmd"> <!-- 변경불가-->
<input type="hidden" name="rqst_data" value="{{ $e_rqstData }}">            <!-- 요청데이터 -->
<input type="hidden" name="target_id" value="{{ $targetId }}">            <!-- 타겟ID -->
<!-- 필수 항목 //-->
</form>

{{-- 인증요청 --}}
@if ($retcode == "B000")
    <script>request();</script>
@endif
