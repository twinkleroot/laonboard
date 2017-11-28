<div id='recaptcha' class="g-recaptcha" data-sitekey="{{ cache("config.recaptcha")->googleInvisibleClient }}" data-callback="onSubmit" data-size="invisible"></div>
<input type="hidden" name="g-recaptcha-response" id="g-response" />
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
function onSubmit(token) {
    $("#g-response").val(token);
    // ajax로 리캡챠 서버쪽 확인
    if(!recaptchaServer()) {
        return false;
    } else {
        var form = $(".submitBtn").closest('form');
        $(form).submit();
    }
}

function recaptchaServer()
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

function recaptchaClient()
{
    grecaptcha.execute();
}

$(function() {
    var clientKey = "{{ cache("config.recaptcha")->googleInvisibleClient }}";
    if(!clientKey) {
        alert("모듈 관리에서 자동등록방지(Google Invisible reCAPTCHA)키가 등록되지 않아서 회원가입을 진행할 수 없습니다. 관리자에게 문의하여 주십시오.");

        location.href="/";
    }
});
@if(request()->segments()[0] === 'bbs')
    $(function(){
        $(document).off('click', '.submitBtn');

        $(document).on('click', '.submitBtn', function(){
        @php
            $userId = auth()->check() ? auth()->user()->id : 0;
        @endphp
        @if(!auth()->check() || (!auth()->user()->isBoardAdmin($board) && isShowCaptchaFromWriteCount($userId) ) )
            recaptchaClient();
        @else
            var form = $(".submitBtn").closest('form');
            $(form).submit();
        @endif
        });
    });
@else
    $(function(){
        $(document).off('click', '.submitBtn');

        $(document).on('click', '.submitBtn', function(){
            recaptchaClient();
        });
    });
@endif
</script>
