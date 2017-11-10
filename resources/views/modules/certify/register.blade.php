@php
    $certConfig = cache('config.cert') ? : json_decode(Config::whereName('config.cert')->first()->vars);
@endphp

@if($certConfig->certHp)
<input type="hidden" name="certType" id="certType" value="">
<input type="hidden" name="name" id="name" value="">
<input type="hidden" name="hp" id="hp" value="">
<input type="hidden" name="certNo" id="certNo" value="">
<input type="hidden" name="adult" id="adult" value="">
<input type="hidden" name="birth" id="birth" value="">
<input type="hidden" name="sex" id="sex" value="">
<input type="hidden" name="dupinfo" id="dupinfo" value="">
@endif

@if($certConfig->certReq && $certConfig->certHp)
<div class="form-group">
    <button type="button" class="btn btn-block btn-sir" id="win_hp_cert">휴대폰 본인확인</button>
</div>
@endif
{{-- @if($certConfig->certIpin)
<div class="form-group">
    <button type="button" class="btn btn-block btn-sir" id="win_ipin_cert">아이핀 본인확인</button>
</div>
@endif --}}

<script>
$(function() {
    // 휴대폰인증
    $("#win_hp_cert").click(function() {
        if(!cert_confirm())
            return false;

        @if($certConfig->certHp == 'kcb')
            certify_win_open("kcb-hp", "{{ route('certify.kcb.hp1')}}");
        @endif

        return;
    });
});
</script>
