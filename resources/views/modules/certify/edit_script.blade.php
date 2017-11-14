<script src="{{ ver_asset('modules/certify/js/certify.js') }}"></script>
<script>
onsubmit = function() {
    // 닉네임 금지어 검사
    if(!filterNickname()) {
        return false;
    }
    // 같은 사람의 본인확인 데이터를 사용했는지 검사
    if(!existCertData()) {
        return false;
    }

    mergeUserData();

    return true;
}
</script>
