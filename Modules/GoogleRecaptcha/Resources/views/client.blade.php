<div id='recaptcha' class="g-recaptcha"
    data-sitekey="{{ cache('config.sns')->googleRecaptchaClient }}"
    data-callback="onSubmit"
    data-size="invisible" style="display:none">
</div>
<input type="hidden" name="g-recaptcha-response" id="g-response" />
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
function onSubmit(token) {
    $("#g-response").val(token);
    $("#fwrite").submit();
}

$(function(){
    $(document).on('click', '.submitBtn', function(){
        if(writeSubmit()) {
            @if( !auth()->check() || !auth()->user()->isBoardAdmin($board) && $board->use_recaptcha && todayWriteCount(auth()->user()->id) > config('laon.todayWriteCount') )
                grecaptcha.execute();
            @else
                $("#fwrite").submit();
            @endif
        }
    });
});
</script>
