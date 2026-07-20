@extends('layouts.app')

@section('content')
    <section>
        @if(!isset($paginatedFavoriteObjects) || $paginatedFavoriteObjects->count() == 0)
            <div class="container my-4">
                <p class="text-center">{{ __('general.no_items_added') }}</p>
            </div>
        @else
            <div class="container my-4">
                <h1 class="text-center mb-4">{{ __('favorites.my_favorites') }}</h1>

                <div class="mb-4" id="selection-controls" style="display: none;">
                    <div class="mt-3 d-flex justify-content-between align-items-center p-3 border rounded shadow-sm bg-white">

                        <span id="selection-info-text">0 {{__('general.selected_objects')}}</span>

                        <div class="d-flex gap-2 align-items-center">
                            <a href="#" id="clear-selection" class="btn btn-link text-decoration-none p-0 me-3">
                                {{ __('favorites.clear_selection') }}
                            </a>

                            <div id="bulk-collections-container">
                                <button type="button" id="remove-from-favorites" class="btn btn-sm btn-danger">
                                    <i class="fa fa-trash-alt"></i>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="row gy-4">
                    <!-- Start object -->
                    <x-cultural-objects-list :cultural-objects="$paginatedFavoriteObjects" :user-likes="$user_likes"/>
                    <!-- End object -->
                </div>
            </div>

            {{ $paginatedFavoriteObjects->links() }}
        @endif
    </section>

@endsection

@push('styles')
    <style>
        .select-object {
            width: 20px;
            height: 20px;
            accent-color: #0d6efd;
            border-radius: 50%;
            cursor: pointer;
        }
        .btn-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            background-color: #f0f0f0;
            color: #333;
        }

        .btn-icon:hover {
            background-color: #dc3545;
            color: #fff;
            transform: scale(1.1);
        }

        .btn-heart {
            background-color: #dc3545;
            color: #fff;
        }
    </style>

@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            $(function () {
                function getSelectedIds() {
                    return $('.select-object:checked').map(function () {
                        return $(this).data('id');
                    }).get();
                }

                function toggleControls() {
                    const count = getSelectedIds().length;

                    $('#selection-info-text').text(count + ' {{__("general.selected_objects")}}');

                    if (count > 0) {
                        $('#selection-controls').show();
                    } else {
                        $('#selection-controls').hide();
                    }
                }

                $(document).on('change', '.select-object', toggleControls);

                $('#clear-selection').on('click', function (e) {
                    e.preventDefault();
                    $('.select-object').prop('checked', false);
                    toggleControls();
                });

                $('#remove-from-favorites').on('click', function () {
                    const selected = getSelectedIds();
                    if (!selected.length) return;

                    $.post({
                        url: '{{ route("profile.favorites.remove-multiple") }}',
                        data: JSON.stringify({ object_ids: selected }),
                        contentType: 'application/json',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).always(() => {
                        window.location.reload();
                    });
                });

                toggleControls();
            });
        });
    </script>

@endpush
