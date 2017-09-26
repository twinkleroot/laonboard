@php
// config
$link_limit = 7; // maximum number of mobile links (a little bit inaccurate, but will be ok for now)
@endphp
@if($paginator->hasPages())
<ul class="pagination">
    @if ($paginator->currentPage() != 1)
    <li><a href="{{ $paginator->url(1) }}">@lang('pagination.first')</a></li>
    @else
    <li class="disabled"><span>@lang('pagination.first')</span></li>
    @endif
@if (isMobile())
    @for ($i = 1; $i <= $paginator->lastPage(); $i++)
        @php
        $half_total_links = floor($link_limit / 2);
        $from = $paginator->currentPage() - $half_total_links;
        $to = $paginator->currentPage() + $half_total_links;
        if ($paginator->currentPage() < $half_total_links) {
           $to += $half_total_links - $paginator->currentPage();
        }
        if ($paginator->lastPage() - $paginator->currentPage() < $half_total_links) {
            $from -= $half_total_links - ($paginator->lastPage() - $paginator->currentPage()) - 1;
        }
        @endphp
        @if ($from < $i && $i < $to)
            <li class="{{ ($paginator->currentPage() == $i) ? ' active' : '' }}">
                <a href="{{ $paginator->url($i) }}">{{ $i }}</a>
            </li>
        @endif
    @endfor
@else
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
        <li class="disabled"><span>@lang('pagination.previous')</span></li>
    @else
        <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">@lang('pagination.previous')</a></li>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
            <li class="disabled"><span>{{ $element }}</span></li>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <li class="active"><span>{{ $page }}</span></li>
                @else
                    <li><a href="{{ $url }}">{{ $page }}</a></li>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
        <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">@lang('pagination.next')</a></li>
    @else
        <li class="disabled"><span>@lang('pagination.next')</span></li>
    @endif
@endif
    @if ($paginator->currentPage() != $paginator->lastPage())
    <li><a href="{{ $paginator->url($paginator->lastPage()) }}">@lang('pagination.last')</a></li>
    @else
    <li class="disabled"><span>@lang('pagination.last')</span></li>
    @endif
</ul>
@endif
