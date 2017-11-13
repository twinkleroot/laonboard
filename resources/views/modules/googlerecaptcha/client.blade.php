<div id='recaptcha' class="g-recaptcha"
    data-sitekey="{{ cache('config.sns')->googleRecaptchaClient }}"
    data-callback="onSubmit"
    data-size="invisible" style="display:none">
</div>
<input type="hidden" name="g-recaptcha-response" id="g-response" />
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
function onSubmit(token) {
    var form = $(".submitBtn").closest('form').attr('id');
    $("#g-response").val(token);
    $(form).submit();
}
@if( !auth()->check() || (!auth()->user()->isBoardAdmin($board) && $board->use_recaptcha) )
@if(request()->segments()[0] == 'bbs' && todayWriteCount(auth()->user()->id) > 0)
$(function(){
    $(document).on('click', '.submitBtn', function(){
        grecaptcha.execute();
    });
});
@endif
@endif
</script>
