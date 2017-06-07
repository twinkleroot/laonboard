<h2>회원 인증 메일입니다.</h2>
<a href="{{ url('/') }}">{{ Cache::get("config.homepage")->title }} </a><br />
아래 주소를 클릭하시면 인증이 완료됩니다.<br /><br />
<a href="{{ $url }}">{{ $url }}</a>
