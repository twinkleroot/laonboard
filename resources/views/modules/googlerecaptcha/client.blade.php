<div id='recaptcha' class="g-recaptcha" data-sitekey="{{ cache("config.recaptcha")->googleInvisibleClient }}" data-callback="onSubmit" data-size="invisible">
</div>
<input type="hidden" name="g-recaptcha-response" id="g-response" />

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
function onSubmit(token) {
    var form = $(".submitBtn").closest('form');
    $("#g-response").val(token);
    // ajax로 리캡챠 서버쪽 확인
    if(!recaptcha()) {
        return false;
    }
    $(form).submit();
}

function recaptcha()
{
    var message = '';

    $.ajax({
        url: '/googlerecaptcha',
        type: 'post',
        data: {
            '_token' : window.Laravel.csrfToken,
            'g-recaptcha-response' : $('#g-response').val(),
        },
        dataType: 'json',
        async: false,
        cache: false,
        success: function(data) {
            message = data.message;
        }
    });

    if(message) {
        alert(message);

        return false;
    }

    return true;
}
@php
    $userId = auth()->check() ? auth()->user()->id : 0;
@endphp

@if(request()->segments()[0] === 'bbs')
    $(function(){
        $('.submitBtn').off('click').on('click', function(){

        @if(!auth()->check() || (!auth()->user()->isBoardAdmin($board) && $board->use_recaptcha && isShowCaptchaFromWriteCount($userId) ) )
            grecaptcha.execute();
        @else
            var form = $(".submitBtn").closest('form');
            $(form).submit();
        @endif
        });
    });
@else
    $(function(){
        $('.submitBtn').off('click').on('click', function(){
            grecaptcha.execute();
        });
    });
@endif
</script>
