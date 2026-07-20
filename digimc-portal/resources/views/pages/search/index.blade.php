@extends('layouts.app')

@section('content')
    <section class="cultural section">

        <div class="container mb-4">
            <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#advancedSearchCollapse">
                <i class="fa fa-search-plus me-2"></i>{{ __('search.advanced_search') }}
                <i class="fa fa-chevron-down ms-2"></i>
            </button>
        </div>


        <div class="collapse container mb-4" id="advancedSearchCollapse">
            <div class="card">
                <div class="card-body">
                    <form id="advanced-search-form" method="GET" action="{{ route('search.index') }}">

                        <input type="hidden" name="q" value="{{ $query }}">

                        @php
                            $tooltips = [
                                'alternative_title' => __('search.tooltip_alternative_title'),
                                'object_type' => __('search.tooltip_object_type'),
                                'issued_date' => __('search.tooltip_issued_date'),
                                'creation_date' => __('search.tooltip_creation_date'),
                                'title' => __('search.tooltip_title'),
                                'publisher' => __('search.tooltip_publisher'),
                                'source' => __('search.tooltip_source'),
                                'current_location' => __('search.tooltip_current_location'),
                                'description' => __('search.tooltip_description'),
                                'rights' => __('search.tooltip_rights'),
                                'provenance' => __('search.tooltip_provenance'),
                                'material' => __('search.tooltip_material'),
                                'creator' => __('search.tooltip_creator'),
                                'theme' => __('search.tooltip_theme'),
                                'format' => __('search.tooltip_format'),
                                'provider' => __('search.tooltip_provider'),
                            ];
                        @endphp

                        <div class="row g-3">
                            @foreach([
                                'alternative_title' => __('search.alternative_title'),
                                'object_type' => __('search.object_type'),
                                'issued_date' => __('search.issued_date'),
                                'creation_date' => __('search.creation_date'),
                                'title' => __('search.title'),
                                'publisher' => __('search.publisher'),
                                'source' => __('search.source'),
                                'current_location' => __('search.current_location'),
                                'description' => __('search.description'),
                                'rights' => __('search.rights'),
                                'provenance' => __('search.provenance'),
                                'material' => __('search.material'),
                                'creator' => __('search.creator'),
                                'theme' => __('search.theme'),
                                'format' => __('search.format'),
                                'provider' => __('search.provider'),
                            ] as $field => $label)
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label small fw-bold">
                                        <i class="fa fa-info-circle text-secondary me-1"
                                           data-bs-toggle="tooltip"
                                           data-bs-placement="top"
                                           title="{{ $tooltips[$field] ?? '' }}">
                                        </i>
                                        {{ $label }}
                                    </label>
                                    <div class="input-group advanced-search-field">
                                        <select class="form-select form-select-sm search-mode" name="adv_{{ $field }}_mode" style="max-width: 150px;"
                                                data-field="{{ $field }}"
                                                data-mode="{{ $advancedFilters[$field]['mode'] ?? '1' }}"
                                        >
                                            <option value="1" {{ (isset($advancedFilters[$field]['mode']) && $advancedFilters[$field]['mode'] == '1') ? 'selected' : '' }}>
                                                {{ __('search.contains') }}
                                            </option>
                                            <option value="0" {{ (isset($advancedFilters[$field]['mode']) && $advancedFilters[$field]['mode'] == '0') ? 'selected' : '' }}>
                                                {{ __('search.not_contains') }}
                                            </option>
                                        </select>

                                        @if($field === 'object_type')
                                            <select class="form-select form-select-sm search-value" name="adv_{{ $field }}" data-field="{{ $field }}">
                                                <option value="">{{ __('search.all_types') }}</option>
                                                @foreach($objectTypes as $code => $typeName)
                                                    <option value="{{ $code }}" {{ (isset($advancedFilters[$field]['value']) && $advancedFilters[$field]['value'] == $code) ? 'selected' : '' }}>
                                                        {{ $typeName }}
                                                    </option>
                                                @endforeach
                                            </select>

                                        @elseif($field === 'format')

                                            <select class="form-select form-select-sm search-value"
                                                    name="adv_{{ $field }}"
                                                    data-field="{{ $field }}">

                                                <option value="">{{ __('search.all_formats') }}</option>

                                                @foreach($mimeTypes as $code => $label)
                                                    <option value="{{ $code }}"
                                                        {{ (isset($advancedFilters[$field]['value']) && $advancedFilters[$field]['value'] == $code) ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>

                                        @else
                                            <input type="text"
                                                   class="form-control form-control-sm search-value"
                                                   name="adv_{{ $field }}"
                                                   value="{{ $advancedFilters[$field]['value'] ?? '' }}"
                                                   placeholder="{{ __('search.' . $field . '_placeholder') }}"
                                                   data-field="{{ $field }}"
                                                   readonly
                                                   style="background-color: #f8f9fa; cursor: pointer;">
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="row mt-4">
                            <div class="col-12 d-flex justify-content-between">

                                <button type="button" class="btn btn-outline-secondary" id="reset-advanced-search">
                                    <i class="fa fa-refresh me-2"></i>{{ __('search.reset') }}
                                </button>

                                <button type="button" class="btn btn-primary" id="run-advanced-search">
                                    <i class="fa fa-search me-2"></i>{{ __('search.apply_filters') }}
                                </button>
                            </div>
                        </div>


                    </form>
                </div>
            </div>
        </div>

        @if(!empty($advancedFilters))
            <div class="container mb-3">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="small fw-bold">{{ __('search.active_filters') }}</span>
                    @foreach($advancedFilters as $field => $filter)
                        @if(!empty($filter['value']))
                            @php
                                $quickFilterFields = ['theme', 'object_type', 'rights', 'provider', 'country_of_origin'];
                            @endphp

{{--                            @continue(in_array($field, $quickFilterFields))--}}

                            <span class="badge bg-light text-dark border">
                                @php
                                    $fieldLabels = [
                                        'alternative_title' => __('search.alternative_title'),
                                        'object_type' => __('search.object_type'),
                                        'issued_date' => __('search.issued_date'),
                                        'creation_date' => __('search.creation_date'),
                                        'title' => __('search.title'),
                                        'publisher' => __('search.publisher'),
                                        'source' => __('search.source'),
                                        'current_location' => __('search.current_location'),
                                        'description' => __('search.description'),
                                        'rights' => __('search.rights'),
                                        'provenance' => __('search.provenance'),
                                        'material' => __('search.material'),
                                        'creator' => __('search.creator'),

                                        'theme' => __('search.theme'),
                                        'format' => __('search.format'),
                                        'provider' => __('search.provider'),
                                        'country_of_origin' => __('search.country_of_origin'),
                                    ];

                                    $displayValue = $filter['value'];

                                    if ($field === 'object_type' && isset($objectTypes[$filter['value']])) {
                                        $displayValue = $objectTypes[$filter['value']];
                                    }

                                    if ($field === 'format' && isset($mimeTypes[$filter['value']])) {
                                        $displayValue = $mimeTypes[$filter['value']];
                                    }


                                @endphp
                                {{ $fieldLabels[$field] }}: {{ $displayValue }}
                                ({{ $filter['mode'] == '1' ? __('search.contains') : __('search.not_contains') }})
                                <a href="#" class="text-danger ms-1 remove-filter-btn" data-field="{{ $field }}">
                                    <i class="fa fa-times"></i>
                                </a>
                            </span>
                        @endif
                    @endforeach
                    <a href="#" class="small text-danger" id="clear-all-filters">
                        {{ __('search.clear_all_filters') }}
                    </a>
                </div>
            </div>
        @endif


        <div id="search-loading" class="container text-center py-4" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">{{ __('search.loading_status') }}</span>
            </div>
            <p class="mt-2 text-muted">{{ __('search.searching') }}...</p>
        </div>

        <div class="container mb-4" id="quick-filters">
            <div class="card">
                <div class="card-body">
                    <form id="quick-filter-form" method="GET" action="{{ route('search.index') }}">
                        <input type="hidden" name="q" value="{{ $query }}">

                        <div class="row g-3 align-items-end">

                            @php
                                $selectedTheme = explode(',', request('theme'));
                                $selectedType = explode(',', request('type'));
                                $selectedRights = explode(',', request('rights_holder'));
                                $selectedProvider = explode(',', request('provider'));
                                $selectedCountry = explode(',', request('country_of_origin'));
                            @endphp

                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">{{ __('search.theme') }}</label>
                                <select name="theme" class="form-select searchable" multiple>
                                    @foreach($availableThemes ?? [] as $theme)
                                        <option value="{{ $theme }}" @selected(in_array($theme, $selectedTheme))>
                                            {{ $themeLabels[$theme] ?? $theme }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">{{ __('search.media_type') }}</label>
                                <select name="type" class="form-select  searchable" multiple>
                                    @foreach($availableTypes ?? [] as $type)
                                        <option value="{{ $type }}" @selected(in_array($type, $selectedType))>
                                            {{ $mediaTypeLabels[$type] ?? $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">{{ __('search.rights') }}</label>
                                <select name="rights_holder" class="form-select searchable" multiple>
                                    @foreach($availableRights ?? [] as $right)
                                        <option value="{{ $right }}" @selected(in_array($right, $selectedRights))>
                                            {{ $rightsLabels[$right] ?? $right }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">{{ __('search.provider') }}</label>
                                <select name="provider" class="form-select searchable" multiple>
                                    @foreach($availableProviders ?? [] as $provider)
                                        <option value="{{ $provider }}" @selected(in_array($provider, $selectedProvider))>
                                            {{ $providerLabels[$provider] ?? $provider }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">{{ __('search.country_of_origin') }}</label>
                                <select name="country_of_origin" class="form-select searchable" multiple>
                                    @foreach($availableCountries ?? [] as $country)
                                        <option value="{{ $country }}" @selected(in_array($country, $selectedCountry))>
                                            {{ $countryLabels[$country] ?? $country }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

{{--                            <div class="col-md-2">--}}
{{--                                <label class="form-label fw-semibold small">{{ __('search.date_from') }}</label>--}}
{{--                                <input type="date" name="date_from" class="form-control " value="{{ request('date_from') }}">--}}
{{--                            </div>--}}

{{--                            <div class="col-md-2">--}}
{{--                                <label class="form-label fw-semibold small">{{ __('search.date_to') }}</label>--}}
{{--                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">--}}
{{--                            </div>--}}

                            <div class="col-md-2 text-end">
                                <button type="button" id="clear-filters" class="btn btn-outline-secondary w-100">
                                    <i class="fa fa-refresh me-1"></i> {{ __('search.clear_filters') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="search-results-container">
            @if(!isset($culturalObjects) || $culturalObjects->count() == 0)
                <div class="container text-center py-5">
                    <i class="fa fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">{{ __('search.no_results_found') }}</h4>
                    @if(!empty($query) || !empty($advancedFilters))
                        <p class="text-muted">{{ __('search.try_different_search') }}</p>
                        <a href="{{ route('search.index') }}" class="btn btn-primary mt-2">
                            {{ __('search.clear_search') }}
                        </a>
                    @endif
                </div>
            @else
                <div class="container">
                    <div class="mb-4" id="bulk-action-bar" style="display: none;">
                        <div class="mt-3 d-flex justify-content-between align-items-center p-3 border rounded shadow-sm bg-white">
                            <span id="selection-info-text">0 {{__('search.selected_objects')}}</span>
                            <div class="d-flex gap-2 align-items-center">
                                <a href="#" id="clear-selection" class="btn btn-link text-decoration-none p-0 me-3">
                                    {{__('search.clear_selection')}}
                                </a>
                                <button id="add-to-favorites" class="btn btn-icon btn-heart p-0 border-0 bg-transparent">
                                    <i class="fa fa-heart"></i> {{ __('search.heart_button') }}
                                </button>
                                <div id="bulk-collections-container">
                                    @livewire('cultural-object.collections', [
                                    'culturalObjectIds' => [],
                                    ], key('bulk-collections-action'))
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row text-end">
                        <form id="export-form" method="GET" action="{{ route('search.export') }}">
                            <input type="hidden" name="q" value="{{ $query }}">
                            @foreach(request()->all() as $key => $value)
                                @if(!in_array($key, ['page', '_token']) && !empty($value))
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach

                            <div class="input-group" style="max-width: 400px; float: right;">
                                <input type="text" name="filename" class="form-control form-control-sm"
                                       placeholder="{{ __('search.export_filename_placeholder') }}"
                                       value="{{ request('filename') ?: 'search_results_' . now()->format('Ymd_His') }}">
                                <button type="submit" class="btn btn-outline-success btn-sm">
                                    <i class="fa fa-download me-1"></i> {{ __('search.export_csv') }}
                                </button>
                                <i class="fa fa-info-circle text-muted ms-2" data-bs-toggle="tooltip" data-bs-placement="top"
                                   title="{{ __('search.export_tooltip') }}"></i>
                            </div>
                        </form>
                    </div>

                    @if(!empty($searchInformationText))
                        @php
                            $formattedSearchInfoText = preg_replace(
                                '/(https?:\/\/[^\s]+)/',
                                '<a href="$1" target="_blank">$1</a>',
                                e($searchInformationText)
                            );
                        @endphp

                        <div class="alert alert-light border d-flex align-items-center mt-3">
                            <i class="fa fa-info-circle text-secondary me-2"></i>

                            <span>
                                {!! $formattedSearchInfoText !!}
                            </span>
                        </div>
                    @endif

                    <div class="row gy-4">
                        <x-cultural-objects-list :cultural-objects="$culturalObjects" :user-likes="$user_likes"/>
                    </div>
                </div>


                <div class="mt-4 d-flex justify-content-center">
                    {{ $culturalObjects->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <style>
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tooltipTriggerList = [].slice.call(
                document.querySelectorAll('[data-bs-toggle="tooltip"]')
            );

            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
    <script>

        $(document).ready(function() {
            $('.searchable').select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                placeholder: $(this).data('placeholder'),
                closeOnSelect: false,
                dropdownParent: $('#quick-filters .card-body'),
                multiple: true,
                language: {
                    noResults: function() {
                        return "{{ __('search.no_results_found') }}";
                    }
                }
            });

            $('.search-mode').each(function() {
                const mode = $(this).data('mode');
                if (mode) {
                    $(this).val(mode);
                }
            });

            function validateSearchValue(value)
            {
                const trimmed = value.trim();
                return trimmed.length >= 3 && trimmed.length <= 255;
            }


            $('#advancedSearchCollapse').on('show.bs.collapse', function () {
                $(this).prev().find('.fa-chevron-down').removeClass('fa-chevron-down').addClass('fa-chevron-up');
            }).on('hide.bs.collapse', function () {
                $(this).prev().find('.fa-chevron-up').removeClass('fa-chevron-up').addClass('fa-chevron-down');
            });

            $('.search-value').on('click', function() {
                $(this).prop('readonly', false)
                    .css({'background-color': '#fff', 'cursor': 'text'})
                    .focus();
            });

            $('.search-value').on('blur', function() {
                if ($(this).val().trim() === '') {
                    $(this).prop('readonly', true)
                        .css({'background-color': '#f8f9fa', 'cursor': 'pointer'});
                }
            });

            $('#reset-advanced-search').on('click', function() {
                clearAllFilters();
            });

            $('#run-advanced-search').on('click', function() {
                performFullSearch();
            });

            $('.search-value').on('keypress', function(e) {
                if (e.which === 13) {
                    performFullSearch();
                }
            });

            $(document).on('click', '#clear-all-filters', function(e) {
                e.preventDefault();
                clearAllFilters();
            });

            function clearAllFilters() {
                $('.search-value').val('').prop('readonly', true)
                    .css({'background-color': '#f8f9fa', 'cursor': 'pointer'});
                $('.search-mode').val('1');

                $('.searchable').val(null).trigger('change');
                $('input[name="date_from"]').val('');
                $('input[name="date_to"]').val('');

                performFullSearch();
            }

            $(document).on('click', '.remove-filter-btn', function(e) {
                e.preventDefault();
                const field = $(this).data('field');

                $(`.search-value[data-field="${field}"]`).val('').prop('readonly', true)
                    .css({'background-color': '#f8f9fa', 'cursor': 'pointer'});
                $(`.search-mode[data-field="${field}"]`).val('1');

                performFullSearch();
            });


            @if(!empty($advancedFilters) || request()->boolean('advanced'))
            $('#advancedSearchCollapse').collapse('show');
            @endif

            @if(request()->boolean('advanced'))
            const url = new URL(window.location.href);
            url.searchParams.delete('advanced');
            window.history.replaceState({}, '', url.toString());
            @endif

            function updateQuickFiltersDisplay($response) {
                const $newQuickFiltersHtml = $response.find('#quick-filters').html();
                $('#quick-filters').html($newQuickFiltersHtml);

                $('.searchable').select2({
                    theme: "bootstrap-5",
                    width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                    placeholder: $(this).data('placeholder'),
                    closeOnSelect: false,
                    dropdownParent: $('#quick-filters .card-body'),
                    multiple: true,
                    language: {
                        noResults: function() {
                            return "{{ __('search.no_results_found') }}";
                        }
                    }
                });
            }

            function performFullSearch() {
                const params = new URLSearchParams();

                const mainQuery = $('#advanced-search-form input[name="q"]').val();
                if (mainQuery) {
                    params.append('q', mainQuery);
                }

                $('.advanced-search-field').each(function() {
                    const field = $(this).find('.search-value').data('field');
                    const value = $(this).find('.search-value').val().trim();
                    const mode = $(this).find('.search-mode').val();

                    const name = 'adv_' + field;

                    if (validateSearchValue(value)) {
                        params.append(name, value);
                        params.append(name + '_mode', mode);
                    }
                });

                $('#quick-filter-form').find('select, input[type="date"]').each(function() {
                    const $this = $(this);
                    const name = $this.attr('name');
                    let values = $this.val();

                    if (name.startsWith('adv_')) { return; }

                    if (Array.isArray(values)) {
                        const combinedValue = values.filter(v => v).join(',');
                        if (combinedValue) {
                            params.append(name, combinedValue);
                        }
                    }
                    else if (values) {
                        params.append(name, values);
                    }
                });

                const url = '{{ route("search.index") }}?' + params.toString();

                $('#search-loading').show();
                $('#search-results-container').hide();

                window.history.pushState({}, '', url);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        const $response = $(response);

                        const $newResults = $response.find('#search-results-container').html();
                        $('#search-results-container').html($newResults);

                        updateActiveFiltersDisplay($response);

                        updateQuickFiltersDisplay($response);

                        initializeEventHandlers();
                    },
                    error: function(xhr) {
                        console.error('Search error:', xhr);
                        alert('{{ __("search.search_error") }}');
                    },
                    complete: function() {
                        $('#search-loading').hide();
                        $('#search-results-container').show();
                    }
                });
            }

            function updateActiveFiltersDisplay($response) {
                const $activeFiltersContainer = $response.find('.container.mb-3').first();
                const currentFiltersContainer = $('.container.mb-3').first();

                if ($activeFiltersContainer.length > 0 && $activeFiltersContainer.html().trim() !== '') {
                    if (currentFiltersContainer.length > 0) {
                        currentFiltersContainer.replaceWith($activeFiltersContainer);
                    } else {
                        $('#advancedSearchCollapse').after($activeFiltersContainer);
                    }
                } else {
                    currentFiltersContainer.remove();
                }
            }

            let searchTimeout;
            function scheduleFullSearch() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(performFullSearch, 400);
            }

            $('#quick-filters').on('change', '.searchable, input[type="date"]', function() {
                scheduleFullSearch();
            });

            $('#quick-filters').on('click', '#clear-filters', function() {
                $('#quick-filters').find('select').each(function() {
                    $(this).val(null).trigger('change');
                });

                $('#quick-filters').find('input[type="date"]').val('');

                performFullSearch();
            });

            // $('.search-value, .search-mode').on('change input', function() {
            //     scheduleFullSearch();
            // });


            window.addEventListener('popstate', function() {
                performFullSearch();
            });


            function getSelectedIds() {
                return $('.select-object:checked').map(function() {
                    return $(this).data('id');
                }).get();
            }

            function updateBulkActions() {
                const selectedIds = getSelectedIds();
                const totalObjects = $('.select-object').length;

                $('#selection-info-text').text(selectedIds.length + ' {{__('search.selected_objects')}}');

                const livewireId = $('#bulk-collections-container').find('[wire\\:id]').attr('wire:id');
                const livewireComponent = Livewire.find(livewireId);

                if (selectedIds.length > 0) {
                    $('#bulk-action-bar').slideDown(200);
                    $('.individual-collections-btn').hide();

                    if (livewireComponent) {
                        livewireComponent.set('culturalObjectIds', selectedIds);
                    }
                } else {
                    $('#bulk-action-bar').slideUp(200);
                    $('.individual-collections-btn').show();
                }

                $('#select-all-checkbox').prop('checked', selectedIds.length === totalObjects && totalObjects > 0);
            }

            function initializeEventHandlers() {
                $(document).off('change', '.select-object').on('change', '.select-object', updateBulkActions);

                $('#select-all-checkbox').off('change').on('change', function() {
                    const isChecked = $(this).prop('checked');
                    $('.select-object').prop('checked', isChecked);
                    updateBulkActions();
                });

                $('#clear-selection').off('click').on('click', function(e) {
                    e.preventDefault();
                    $('.select-object').prop('checked', false);
                    updateBulkActions();
                });

                $('#add-to-favorites').off('click').on('click', function (e) {
                    e.preventDefault();
                    const selected = getSelectedIds();
                    if (selected.length === 0) return;

                    $.ajax({
                        url: '{{ route("profile.favorites.add-multiple") }}',
                        type: 'POST',
                        data: JSON.stringify({ object_ids: selected }),
                        contentType: 'application/json',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        success: function (response) {
                            $('.select-object').prop('checked', false);
                            updateBulkActions();
                            Livewire.dispatch('refreshLikes');
                        },
                        error: function (xhr) {
                            console.error('Error adding favorites:', xhr);
                        }
                    });
                });

                updateBulkActions();
            }

            initializeEventHandlers();
        });
    </script>
@endpush
