@if(cache('config.cert')->certHp)
    <input type="hidden" name="certType" value="">
    <input type="hidden" name="certNo" value="">
    <input type="hidden" name="adult" id="adult" value="">
    <input type="hidden" name="birth" id="birth" value="">
    <input type="hidden" name="sex" id="sex" value="">
    <input type="hidden" name="dupinfo" id="dupinfo" value="">
    <input type="hidden" name="certify" id="certify" value="">
    @unless($config->name)
    <input type="hidden" name="name" value="">
    @endunless
    @unless ($config->hp)
    <input type="hidden" name="hp" value="">
    @endunless
@endif

@if($user->certify)
<div class="help bg-info mb10">
    <span class="cert">휴대폰 본인확인 및 성인인증 완료</span>
</div>
@endif
@if(cache('config.cert'))
<div class="help bg-danger mb10">
    <span class="warning">휴대폰 본인확인 후에는 이름과 휴대폰번호가 자동 입력되어 수동으로 입력할 수 없게 됩니다.</span>
</div>
@endif
@if((cache('config.cert')->certUse && cache('config.cert')->certHp) || $config->name) <!-- 이름 -->
<div class="form-group">
    <label for="name" class="control-label">이름</label>
    <input id="name" type="text" class="form-control" name="name" value="{{ $user->name }}" @if($user->certify) readonly @endif>
</div>
@endif
@if(cache('config.cert')->certHp)
<div class="form-group row">
    <div class="col-md-12 mb10">
        <input type="button" class="btn btn-block btn-default btn_frmline" id="win_hp_cert" value="휴대폰 본인확인">
    </div>
</div>
@endif

@if( (cache('config.cert')->certUse && cache('config.cert')->certHp) || $config->hp) <!-- 휴대폰번호 -->
<div class="form-group">
    <label for="hp" class="control-label">휴대폰번호</label>
    <input id="hp" type="text" class="form-control" name="hp" value="{{ $user->hp }}">
</div>
@endif

<script>
$(function(){

    // 휴대폰인증
    $("#win_hp_cert").click(function() {
        if(!cert_confirm())
            return false;

        @if(cache('config.cert')->certHp == 'kcb')
            certify_win_open("kcb-hp", "{{ route('certify.kcb.hp1')}}");
        @endif

        return;
    });

});
</script>
