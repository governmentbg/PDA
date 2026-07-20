@extends('layouts.app')

@section('content')
    <section class="cultural section">
        <div class="container my-4">
            <h2>{{ $gallery->name }}</h2>

            <div id="status-message" class="alert d-none mt-3" role="alert"></div>
            <div class="mb-4" id="bulk-action-bar" style="display: none;">
                <div class="mt-3 d-flex justify-content-between align-items-center p-3 border rounded shadow-sm bg-white">

                    <span id="selection-info-text">0 {{__('general.selected_objects')}}</span>

                    <div class="d-flex gap-2 align-items-center">
                        <a href="#" id="clear-selection" class="btn btn-link text-decoration-none p-0 me-3">
                            {{__('general.clear_selection')}}
                        </a>

                        <div id="bulk-collections-container">
                            <button type="button" id="bulk-remove-object" class="btn btn-sm btn-danger bulk-remove-object">
                                <i class="fa fa-trash-alt"></i>
                            </button>
                        </div>

                    </div>
                </div>
            </div>
            <div class="mb-3 d-flex align-items-center gap-2">
                <livewire:gallery.share-switch :gallery-id="$gallery->id" :status="$gallery->status" />
            </div>
            @if(!empty($gallery->description))
            <p class="text-muted" style="white-space: pre-wrap; word-wrap: break-word;">
                {{ $gallery->description }}
            </p>
            @endif


            @if($gallery->objects->isEmpty())
                <p>{{ __('gallery.no_objects_in_collection') }}</p>
            @else
                <div class="row gy-4">
                    <!-- Start object -->
                    <x-cultural-objects-list :cultural-objects="$gallery->objects" :user-likes="$user_likes"/>
                    <!-- End object -->
                </div>
                <div class="d-flex justify-content-center mt-5">
                    {{ $gallery->objects->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            function updateBulkActions() {
                const selectedIds = $('.select-object:checked').map(function() {
                    return $(this).data('id');
                }).get();
                const totalObjects = $('.select-object').length;

                $('#selection-info-text').text(selectedIds.length + ' {{__('general.selected_objects')}}');

                if (selectedIds.length > 0) {
                    $('#bulk-action-bar').slideDown(200);
                    $('.individual-collections-btn').hide();
                } else {
                    $('#bulk-action-bar').slideUp(200);
                    $('.individual-collections-btn').show();
                }

                $('#select-all-checkbox').prop('checked', selectedIds.length === totalObjects && totalObjects > 0);
            }

            $(document).on('change', '.select-object', updateBulkActions);

            $('#select-all-checkbox').on('change', function() {
                const isChecked = $(this).prop('checked');
                $('.select-object').prop('checked', isChecked);
                updateBulkActions();
            });

            $('#clear-selection').on('click', function(e) {
                e.preventDefault();
                $('.select-object').prop('checked', false);
                updateBulkActions();
            });

            updateBulkActions();

            $('.remove-object').click(function() {
                const objectId = $(this).data('object-id');
                const galleryId = "{{ $gallery->id }}";
                const confirmMessage = $(this).data('confirm-message');
                const errorMessage = $(this).data('error-message');
                const genericError = $(this).data('generic-error');

                if (!confirm(confirmMessage)) return;

                $.ajax({
                    url: "{{ route('profile.galleries.removeObjects') }}",
                    type: "POST",
                    data: {
                        gallery_id: galleryId,
                        object_ids: [objectId],
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success === true) {
                            const selector = `#object-${objectId}`;

                            $(selector).remove();

                            const successMessage = "{{ __('gallery.object_removed_successfully') }}";
                            $('#status-message')
                                .text(successMessage)
                                .removeClass('d-none alert-danger')
                                .addClass('alert-success');
                        } else {
                            $('#status-message')
                                .text(response.message || errorMessage)
                                .removeClass('d-none alert-success')
                                .addClass('alert-danger');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error:", status, error);
                        $('#status-message')
                            .text(genericError)
                            .removeClass('d-none alert-success')
                            .addClass('alert-danger');
                    }
                });
            });

            $('#bulk-remove-object').on('click', function () {
                const selected = $('.select-object:checked').map(function() {
                    return $(this).data('id');
                }).get();
                const galleryId = "{{ $gallery->id }}";

                $.ajax({
                    url: "{{ route('profile.galleries.removeObjects') }}",
                    type: "POST",
                    data: {
                        gallery_id: galleryId,
                        object_ids: selected,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success === true) {
                            selected.forEach(function(objectId) {
                                $('#object-' + objectId).remove();
                            });

                            $('#status-message')
                                .text("{{ __('gallery.objects_removed_successfully') }}")
                                .removeClass('d-none alert-danger')
                                .addClass('alert-success');

                            $('.select-object').prop('checked', false);
                            $('#bulk-action-bar').slideUp(200);
                            $('#selection-info-text').text('0 {{__("general.selected_objects")}}');

                        } else {
                            $('#status-message')
                                .text(response.message || "{{ __('gallery.error_on_remove_objects') }}")
                                .removeClass('d-none alert-success')
                                .addClass('alert-danger');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error:", status, error);
                        $('#status-message')
                            .text("{{ __('gallery.generic_error') }}")
                            .removeClass('d-none alert-success')
                            .addClass('alert-danger');
                    }
                });
            });
        });

    </script>
@endpush
