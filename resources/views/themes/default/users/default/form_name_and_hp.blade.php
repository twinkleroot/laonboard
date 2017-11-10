@if($user->certify)
<div class="help bg-info mb10">
    <span class="cert">성인인증 완료</span>
</div>
@endif
@if($config->name) <!-- 이름 -->
<div class="form-group @if($errors->has('name'))has-error @endif">
    <label for="name" class="control-label">이름</label>
    <input id="name" type="text" class="form-control" name="name" value="{{ $user->name }}" @if($user->certify) readonly @endif>
    @if ($errors->has('name'))
    <span class="help-block">
        <strong>{{ $errors->first('name') }}</strong>
    </span>
    @endif
</div>
@endif

@if($config->hp) <!-- 휴대폰번호 -->
<div class="form-group @if($errors->has('hp'))has-error @endif">
    <label for="hp" class="control-label">휴대폰번호</label>
    <input id="hp" type="text" class="form-control" name="hp" value="{{ $user->hp }}">
    @if ($errors->has('hp'))
    <span class="help-block">
        <strong>{{ $errors->first('hp') }}</strong>
    </span>
    @endif
</div>
@endif
