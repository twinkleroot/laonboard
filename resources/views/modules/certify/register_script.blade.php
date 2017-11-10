<script src="{{ url('modules/certify/js/certify.js') }}"></script>
<script>
function userFormSubmit(form)
{
    if(!validateCertBeforeJoin()) {
        return false;
    }
    if(!existCertData()) {
        return false;
    }

    mergeUserData();

    return true;
}
</script>
