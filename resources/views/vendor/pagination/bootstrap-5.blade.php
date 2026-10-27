@if ($paginator->hasPages())
    <nav class="d-flex flex-wrap align-items-center justify-content-between gap-2 w-100" role="navigation" aria-label="Paginação">
        {{-- Modo Mobile --}}
        <div class="d-flex justify-content-between flex-fill d-sm-none">
            <ul class="pagination pagination-sm mb-0">
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link"><i class="fas fa-arrow-left me-1"></i> Anterior</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev"><i class="fas fa-arrow-left me-1"></i> Anterior</a>
                    </li>
                @endif

                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Próximo <i class="fas fa-arrow-right ms-1"></i></a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">Próximo <i class="fas fa-arrow-right ms-1"></i></span>
                    </li>
                @endif
            </ul>
        </div>

        {{-- Modo Desktop / Tablet --}}
        <div class="d-none d-sm-flex align-items-center justify-content-between w-100">
            <div class="small text-secondary">
                Exibindo de
                <span class="fw-bold text-body">{{ $paginator->firstItem() }}</span>
                a
                <span class="fw-bold text-body">{{ $paginator->lastItem() }}</span>
                de
                <span class="fw-bold text-body">{{ $paginator->total() }}</span>
                registros
            </div>

            <div>
                <ul class="pagination pagination-sm mb-0">
                    {{-- Link Página Anterior --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true" aria-label="Anterior">
                            <span class="page-link" aria-hidden="true"><i class="fas fa-chevron-left" style="font-size: 0.72rem;"></i></span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Anterior"><i class="fas fa-chevron-left" style="font-size: 0.72rem;"></i></a>
                        </li>
                    @endif

                    {{-- Elementos de Paginação (Páginas e Reticências) --}}
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                        @endif

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

                    {{-- Link Próxima Página --}}
                    @if ($paginator->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Próximo"><i class="fas fa-chevron-right" style="font-size: 0.72rem;"></i></a>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true" aria-label="Próximo">
                            <span class="page-link" aria-hidden="true"><i class="fas fa-chevron-right" style="font-size: 0.72rem;"></i></span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
@endif
