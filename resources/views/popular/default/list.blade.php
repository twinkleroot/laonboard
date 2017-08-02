<section id="popular">
    <div class="container">
        <h2>인기검색어</h2>
        <ul>
            @foreach(getPopularWords(30) as $popular)
            <li><a href="{{ route('search')."?kind=subject||content&keyword={$popular->word}&operator=and" }}">{{ $popular->word }}</a></li>
            @endforeach
        </ul>
    </div>
</section>
