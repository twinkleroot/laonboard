<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><h3>{{ $userFromSocial->getNickname() }}님 환영합니다.</h3></div>

                    <div class="panel-body">
                        <h4>새로운 회원 가입</h4>
                    </div>

                    <div class="panel-body">
                    <form method="POST" action="{{ route('social.socialUserJoin') }}"
                        onsubmit="return joinValidation(this);" autocomplete="off">
                        {{ csrf_field() }}
                        <input type="hidden" name="provider" value="{{ $provider }}" />
                        <p>
                            <span class="help-block">
                                <strong>{{ $message['password'] }}</strong>
                            </span>
                            비밀번호 <input type="password" name="password" minlength="3" maxlength="20" required />
                        </p>
                        <p>
                            비밀번호 확인 <input type="password" name="password_confirmation"
                                            minlength="3" maxlength="20" required />
                        </p>
                        <p>
                            @if(array_has($message, 'nick'))
                            <span class="help-block">
                                <strong>{{ $message['nick'] }}</strong>
                            </span>
                            @endif
                            닉네임 <input type="text" name="nick" value="{{ $userFromSocial->nickname }}" required />
                        </p>
                        <p>
                            @if(array_has($message, 'email'))
                            <span class="help-block">
                                <strong>{{ $message['email'] }}</strong>
                            </span>
                            @endif
                            이메일 <input type="email" name="email" value="{{ $userFromSocial->email }}" required />
                        </p>
                        <input type="submit" id="userJoin" value="회원가입"/>
                    </form>
                    </div>

                    <div class="panel-body">
                        <hr />
                    </div>

                    <div class="panel-body">
                        <h4>기존 계정과 연결</h4>
                    </div>
                    <div class="panel-body">
                    <form method="POST" action="{{ route('social.connectExistAccount') }}"
                        onsubmit="return loginValidation(form);" autocomplete="off">
                        {{ csrf_field() }}
                        <p>
                            <input type="hidden" name="provider" value="{{ $provider }}" />
                            기존 이메일 <input type="text" name="email" maxlength="20" required />
                            비밀번호 <input type="password" name="password" maxlength="20" required />
                        </p>
                        <input type="submit" id="connectExistAccount" value="연결하고 로그인하기"/>
                    </form>
                    </div>
            </div>
        </div>
    </div>
</div>
