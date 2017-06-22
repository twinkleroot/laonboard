<h2>비밀번호 재설정 메일입니다.</h2>
<a href="{{ url('/') }}">{{ Cache::get("config.homepage")->title }} </a><br />
아래 주소를 클릭하시면 비밀번호 재설정 페이지로 연결됩니다.<br /><br />
<a href="{{ route('password.reset', $token) }}">{{ route('password.reset', $token) }}</a>
