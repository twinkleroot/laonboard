<h2>{{ $exception->getMessage() }}</h2>
<p>
    존재하지 않는 페이지입니다. 확인 후 다시 시도해 주십시오.
</p>

<button type="button" onclick="history.back();">이전페이지로</button>
<a href="{{ route('home') }}">홈페이지 메인</a>
