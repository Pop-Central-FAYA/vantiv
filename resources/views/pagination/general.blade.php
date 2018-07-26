@if ($paginator->hasPages())
    {{--my custom--}}
    <div class="pager">
        @if ($paginator->onFirstPage())
            <a class="disabled"><span>&laquo;</span></a>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="prev"></a>
        @endif

        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="disabled"><span>{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <a href="" class='active'>{{ $page }}</a>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="next"></a>
        @else
            <a class="disabled"><span>&raquo;</span></a>
        @endif

    </div>

@endif
