@if ($paginator->hasPages())
    <ul class="pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="disabled"><span>@unless(isMobile())&laquo;@else @lang('pagination.previous') @endunless</span></li>
        @else
            <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">@unless(isMobile())&laquo;@else @lang('pagination.previous') @endunless</a></li>
        @endif

        {{-- Pagination Elements --}}
        @unless(isMobile())
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
        @endunless

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">@unless(isMobile())&raquo;@else @lang('pagination.next') @endunless</a></li>
        @else
            <li class="disabled"><span>@unless(isMobile())&raquo;@else @lang('pagination.next') @endunless</span></li>
        @endif
    </ul>
@endif
