<script src="{{ ver_asset('modules/certify/js/certify.js') }}"></script>
<script>
onsubmit = function() {
    // 닉네임 금지어 검사
    if(!filterNickname()) {
        return false;
    }
    // 회원 가입전 본인확인 여부 검사
    if(!validateCertBeforeJoin()) {
        return false;
    }
    // 같은 사람의 본인확인 데이터를 사용했는지 검사
    if(!existCertData()) {
        return false;
    }

    // 사용자 데이터에 본인확인 데이터를 포함시킨다.
    mergeUserData();

    return true;
}
</script>
