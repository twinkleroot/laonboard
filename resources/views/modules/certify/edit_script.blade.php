<script src="{{ url('modules/certify/js/certify.js') }}"></script>
<script>
function userFormSubmit(form)
{
    if(!existCertData()) {
        return false;
    }

    mergeUserData();

    return true;
}
</script>
