@extends('themes.default.basic')

@section('title')
    {{ $content->subject }} | {{ Cache::get("config.homepage")->title }}
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        @if(session()->get('admin'))
                        <div class="ctt_admin">
                            <a href="{{ route('contents.edit', $content->id)}}" class="btn_admin">내용 수정</a>
                        </div>
                        @endif
                        <!-- 상단 이미지 -->
                        @if($existHeadImage)
                            <div id="ctt_himg" class="ctt_img">
                                <img src="/storage/content/{{ $content->content_id }}_h" alt="">
                            </div>
                        @endif

                        @if($content->skin == '')
                            <article>
                                <header>
                                    <h1>{{ $content->subject }}</h1>
                                </header>

                                <div>
                                    {!! App\Common\Util::convertContent($content->content, $content->html, $content->tag_filter_use) !!}
                                </div>
                            </article>
                        @else
                            @include('themes.'. $content->skin. '.content')
                        @endif

                        <!-- 하단 이미지 -->
                        @if($existTailImage)
                            <div id="ctt_timg" class="ctt_img">
                                <img src="/storage/content/{{ $content->content_id }}_t" alt="">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
