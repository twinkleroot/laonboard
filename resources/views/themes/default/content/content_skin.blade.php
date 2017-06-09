@section('fisrt_include_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/'. $content->skin. '/css/style.css') }}">
@endsection

<article id="ctt" class="ctt_{{ $content->id }}">
    <header>
        <h1>{{ $content->subject }}</h1>
    </header>

    <div id="ctt_con">
        {!! App\Common\Util::convertContent($content->content, $content->html, $content->tag_filter_use) !!}
    </div>
</article>
