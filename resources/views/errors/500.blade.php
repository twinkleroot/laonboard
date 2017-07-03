<h2>{{ $exception->getMessage() }}</h2>
<p>
    처리 중 오류가 발생하였습니다. 다시 시도하셔도 같은 오류가 발생하면 관리자에게 문의하여 주십시오.
</p>

<button type="button" onclick="history.back();">이전페이지로</button>
<a href="{{ route('home') }}">홈페이지 메인</a>
