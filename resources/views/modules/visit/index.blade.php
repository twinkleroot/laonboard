<link rel="stylesheet" type="text/css" href="{{ ver_asset('modules/visit/css/style.css') }}">
<div class="link">
    <section id="visit">
        <div class="container">
            <h2>접속자집계</h2>
            <dl>
                <dt>오늘</dt>
                <dd>{{ $todayCount }}</dd>
                <dt>어제</dt>
                <dd>{{ $yesterdayCount }}</dd>
                <dt>최대</dt>
                <dd>{{ $visitMax }}</dd>
                <dt>전체</dt>
                <dd>{{ $visitTotal }}</dd>
            </dl>
        </div>
    </section>
</div>
