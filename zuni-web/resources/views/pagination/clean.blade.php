@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginação" class="flex justify-center">

        <div class="join border border-base-200 rounded-lg bg-base-100">

            {{-- ANTERIOR --}}
            @if ($paginator->onFirstPage())
                <span class="join-item btn btn-sm btn-ghost btn-disabled text-base-content/30">‹</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="join-item btn btn-sm btn-ghost text-Cprimary hover:bg-base-200">‹</a>
            @endif

            {{-- PÁGINAS --}}
            @foreach ($elements as $element)

                @if (is_string($element))
                    <span class="join-item btn btn-sm btn-ghost btn-disabled text-base-content/40">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="join-item btn btn-sm bg-Cprimary text-white border-none pointer-events-none">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="join-item btn btn-sm btn-ghost text-base-content/70 hover:bg-base-200 hover:text-Cprimary">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif

            @endforeach

            {{-- PRÓXIMA --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="join-item btn btn-sm btn-ghost text-Cprimary hover:bg-base-200">›</a>
            @else
                <span class="join-item btn btn-sm btn-ghost btn-disabled text-base-content/30">›</span>
            @endif

        </div>

    </nav>
@endif