@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 w-100">
        
        {{-- Modern Card Badge Information (Left) --}}
        <div class="d-flex align-items-center gap-2 p-2 px-3 bg-white border shadow-sm rounded-4 text-muted small">
            <i class="bi bi-table text-primary fs-6"></i>
            
            {{-- Desktop Layout --}}
            <div class="d-none d-md-inline-flex align-items-center gap-1">
                <span>Menampilkan</span>
                <strong class="text-primary font-monospace">{{ $paginator->firstItem() ?? 0 }}–{{ $paginator->lastItem() ?? 0 }}</strong>
                <span>dari</span>
                <strong class="text-dark font-monospace">{{ $paginator->total() }}</strong>
                <span>Data</span>
                <span class="mx-1 text-secondary opacity-50">|</span>
                <span>Halaman</span>
                <strong class="text-success font-monospace">{{ $paginator->currentPage() }}/{{ $paginator->lastPage() }}</strong>
            </div>

            {{-- Mobile Compact Layout --}}
            <div class="d-inline-flex d-md-none align-items-center gap-1">
                <strong class="text-dark font-monospace">{{ $paginator->total() }}</strong>
                <span>Data</span>
                <span class="mx-1 text-secondary opacity-50">•</span>
                <span>Halaman</span>
                <strong class="text-success font-monospace">{{ $paginator->currentPage() }}/{{ $paginator->lastPage() }}</strong>
            </div>
        </div>

        {{-- Pagination Navigation Buttons (Right) --}}
        <ul class="pagination mb-0">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link" aria-hidden="true">&lsaquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link" aria-hidden="true">&rsaquo;</span>
                </li>
            @endif
        </ul>

    </nav>
@endif
