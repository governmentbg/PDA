@if ($paginator->hasPages())
    <div class="container">
        <nav class="pagination-nav-input" id="custom-pagination-nav">
            <ul class="pagination d-flex justify-content-center align-items-center list-unstyled pl-0">

                <li class="page-item btn-first @if ($paginator->onFirstPage()) disabled @endif">
                    <a href="{{ $paginator->url(1) }}"
                       class="page-link smart-link btn-text @if ($paginator->onFirstPage()) disabled @endif"
                       aria-label="{{ __('pagination.first_page') }}">
                        {!! __('pagination.first_page') ?? 'First' !!}
                    </a>
                </li>

                <li class="page-item btn-prev @if ($paginator->onFirstPage()) disabled @endif">
                    <a href="{{ $paginator->previousPageUrl() }}"
                       class="page-link smart-link btn-text @if ($paginator->onFirstPage()) disabled @endif"
                       aria-label="{{ __('pagination.previous_page') }}">
                        {!! __('pagination.previous_page') !!}
                    </a>
                </li>


                <li class="page-item page-input d-flex align-items-center page-input-container">
                    <input name="page"
                           type="number"
                           min="1"
                           max="{{ $paginator->lastPage() }}"
                           class="form-control text-center page-input-no-spinners"
                           aria-label="{{ __('pagination.page_number') }}"
                           value="{{ $paginator->currentPage() }}"
                           id="pagination-page-input-{{ $paginator->getPageName() }}">
                    <span class="text-muted page-total-count"> / {{ $paginator->lastPage() }}</span>
                </li>


                <li class="page-item btn-next @if (!$paginator->hasMorePages()) disabled @endif">
                    <a href="{{ $paginator->nextPageUrl() }}"
                       class="page-link smart-link btn-text @if (!$paginator->hasMorePages()) disabled @endif"
                       aria-label="{{ __('pagination.next_page') }}">
                        {!! __('pagination.next_page') !!}
                    </a>
                </li>


                <li class="page-item btn-last @if (!$paginator->hasMorePages()) disabled @endif">
                    <a href="{{ $paginator->url($paginator->lastPage()) }}"
                       class="page-link smart-link btn-text @if (!$paginator->hasMorePages()) disabled @endif"
                       aria-label="{{ __('pagination.last_page') }}">
                        {!! __('pagination.last_page') ?? 'Last' !!}
                    </a>
                </li>

            </ul>
        </nav>
    </div>

    @push('styles')
        <style>
            .page-input-no-spinners::-webkit-outer-spin-button,
            .page-input-no-spinners::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
            .page-input-no-spinners {
                -moz-appearance: textfield;
            }
            .pagination-nav-input .page-link {
                border: none;
                background-color: transparent;
                color: #007bff;
                font-weight: 500;
                padding: 0.375rem 0.5rem;
                text-decoration: none;
            }
            .pagination-nav-input .page-link.disabled {
                color: #6c757d;
                cursor: not-allowed;
            }
            .pagination-nav-input .page-item {
                display: flex;
                align-items: center;
                margin: 0;
            }
            .page-input-container {
                flex-shrink: 0;
                gap: 5px;
                white-space: nowrap;
            }
            .page-input-container input {
                width: 50px !important;
                height: calc(1.5em + 0.5rem + 2px);
                padding: 0.125rem 0.25rem;
                font-size: 0.875rem;
            }
            .page-input-container .page-total-count {
                font-size: 0.875rem;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            $(document).ready(function() {
                const $pageInput = $('#pagination-page-input-{{ $paginator->getPageName() }}');
                const urlParams = new URLSearchParams(window.location.search);
                const maxPage = parseInt($pageInput.attr('max'));
                const minPage = 1;
                const baseUrl = window.location.href.split('?')[0];

                const goToPage = () => {
                    let requestedPage = parseInt($pageInput.val());
                    const currentPage = {{ $paginator->currentPage() }};

                    if (isNaN(requestedPage) || requestedPage < minPage) {
                        requestedPage = minPage;
                    } else if (requestedPage > maxPage) {
                        requestedPage = maxPage;
                    }

                    if (requestedPage === currentPage) {
                        return;
                    }

                    urlParams.set('{{ $paginator->getPageName() }}', requestedPage);

                    window.location.href = baseUrl + '?' + urlParams.toString();
                };

                $pageInput.on('blur', goToPage);

                $pageInput.on('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        goToPage();
                    }
                });
            });
        </script>
    @endpush

@endif
