@extends('layouts.app')

@section('title')
    LaBoard | 회원 정보 수정
@endsection

@section('include_script')
    <script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
    <script src="{{ url('js/postcode.js') }}"></script>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <script src="{{ asset('js/postcode.js') }}"></script>
                <div class="panel-heading">@lang('messages.user_update_form')</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('user.update') }}">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}
                        <div class="panel-heading">@lang('messages.tab1')</div>

                        <div class="form-group">
                            <label for="email_readonly" class="col-md-4 control-label">@lang('messages.email')</label>
                            <div class="col-md-6">
                                <input type="email" class="form-control" name="email_readonly" value="{{ $user->email }}" readonly>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">@lang('messages.password')</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" class="col-md-4 control-label">@lang('messages.password_confirmation')</label>
                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="panel-heading">@lang('messages.tab2')</div>

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">@lang('messages.name')</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ $user->name }}" readonly>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if($nickChangable)
                        <div class="form-group{{ $errors->has('nick') ? ' has-error' : '' }}">
                            <label for="nick" class="col-md-4 control-label">@lang('messages.nick')</label>

                            <div class="col-md-6">
                                <p>
                                    공백없이 한글, 영문, 숫자만 입력 가능 <br />
                                    (한글2자, 영문4자 이상)<br />
                                    닉네임을 바꾸시면 {{ config('gnu.nickDate') }}일 이내에는 변경할 수 없습니다.
                                </p>
                                <input id="nick" type="text" class="form-control" name="nick" value="{{ $user->nick }}" required autofocus>

                                @if ($errors->has('nick'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nick') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if(config('gnu.homepage') == 1)
                        <div class="form-group{{ $errors->has('homepage') ? ' has-error' : '' }}">
                            <label for="homepage" class="col-md-4 control-label">@lang('messages.homepage')</label>

                            <div class="col-md-6">
                                <input id="homepage" type="text" class="form-control" name="homepage" value="{{ $user->homepage }}">

                                @if ($errors->has('homepage'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('homepage') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if(config('gnu.tel') == 1)
                        <div class="form-group{{ $errors->has('tel') ? ' has-error' : '' }}">
                            <label for="tel" class="col-md-4 control-label">@lang('messages.tel')</label>

                            <div class="col-md-6">
                                <input id="tel" type="text" class="form-control" name="tel" value="{{ $user->tel }}">

                                @if ($errors->has('tel'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('tel') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if(config('gnu.hp') == 1)
                        <div class="form-group{{ $errors->has('hp') ? ' has-error' : '' }}">
                            <label for="hp" class="col-md-4 control-label">@lang('messages.hp')</label>

                            <div class="col-md-6">
                                <input id="hp" type="text" class="form-control" name="hp" value="{{ $user->hp }}">

                                @if ($errors->has('hp'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('hp') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if(config('gnu.addr') == 1)
                        <div class="form-group">
                            <label for="addr1" class="col-md-4 control-label">@lang('messages.address')</label>
                            <div class="col-md-6">

                                <input type="text" id="zip" name="zip" class="form-control" value="{{ $user->zip }}" placeholder="@lang('messages.zip')">
                                <input type="button" onclick="execDaumPostcode()" value="@lang('messages.address_search')"><br>

                                <div id="wrap" style="display:none;border:1px solid;width:500px;height:300px;margin:5px 0;position:relative">
                                    <img src="//t1.daumcdn.net/localimg/localimages/07/postcode/320/close.png"
                                        style="cursor:pointer;position:absolute;right:0px;top:-1px;z-index:1"
                                         id="btnFoldWrap" onclick="foldDaumPostcode()" alt="접기 버튼">
                                </div>
                                <input type="text" id="addr1" name="addr1" class="form-control" value="{{ $user->addr1 }}" placeholder="@lang('messages.address1')">
                                <input type="text" id="addr2" name="addr2" class="form-control" value="{{ $user->addr2 }}" placeholder="@lang('messages.address2')">
                            </div>
                        </div>
                        @endif

                        <div class="panel-heading">@lang('messages.tab3')</div>

                        @if(config('gnu.signature') == 1)
                        <div class="form-group">
                            <label for="signature" class="col-md-4 control-label">@lang('messages.signature')</label>

                            <div class="col-md-6">
                                <textarea name="signature" class="form-control">{{ $user->signature }}</textarea>
                            </div>
                        </div>
                        @endif

                        @if(config('gnu.profile') == 1)
                        <div class="form-group">
                            <label for="profile" class="col-md-4 control-label">@lang('messages.profile')</label>

                            <div class="col-md-6">
                                <textarea name="profile" class="form-control">{{ $user->profile }}</textarea>
                            </div>
                        </div>
                        @endif

                        <div class="form-group{{ $errors->has('mailing') ? ' has-error' : '' }}">
                            <label for="mailing" class="col-md-4 control-label">@lang('messages.mailing')</label>

                            <div class="col-md-6">
                                <input id="mailing" type="checkbox" name="mailing" value="1" @if($user->mailing == 1) checked @endif>
                                @lang('messages.mailing_info')

                                @if ($errors->has('mailing'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('mailing') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('sms') ? ' has-error' : '' }}">
                            <label for="sms" class="col-md-4 control-label">@lang('messages.sms')</label>

                            <div class="col-md-6">
                                <input id="sms" type="checkbox" name="sms" value="1" @if($user->sms == 1) checked @endif>
                                @lang('messages.sms_info')

                                @if ($errors->has('sms'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('sms') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('open') ? ' has-error' : '' }}">
                            <label for="open" class="col-md-4 control-label">@lang('messages.open')</label>

                            @if($openChangable)
                                <div class="col-md-6">
                                    <p>
                                        @lang('messages.open_info1', ['openDate' => config('gnu.openDate')] )
                                    </p>
                                    <input id="open" type="checkbox" name="open" value="1" @if($user->open == 1) checked @endif>
                                    @lang('messages.open_info2')

                                    @if ($errors->has('open'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('open') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            @else
                                <div class="col-md-6">
                                    <p>
                                        정보공개는 수정후 {{ config('gnu.openDate') }}일 이내,
                                        {{ $dueDate->year }}년
                                        {{ $dueDate->month }}월
                                        {{ $dueDate->day }}일 까지는 변경이 안됩니다.<br />
                                        이렇게 하는 이유는 잦은 정보공개 수정으로 인하여 쪽지를 보낸 후 받지 않는 경우를 막기 위해서 입니다.
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    @lang('messages.change')
                                </button>
                                <a class="btn btn-primary" href="{{ route('index') }}">@lang('messages.cancel')</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
