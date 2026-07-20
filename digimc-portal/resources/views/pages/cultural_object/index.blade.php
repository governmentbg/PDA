@extends('layouts.app')

@section('content')
    <section class="cultural section">
        @if(!isset($culturalObjects) || $culturalObjects->count() == 0)
            {{__('general.no_items_added')}}
        @else

            <div class="container">

                <div class="mb-4" id="bulk-action-bar" style="display: none;">
                    <div
                        class="mt-3 d-flex justify-content-between align-items-center p-3 border rounded shadow-sm bg-white">

                        <span id="selection-info-text">0 {{__('general.selected_objects')}}</span>

                        <div class="d-flex gap-2 align-items-center">
                            <a href="#" id="clear-selection" class="btn btn-link text-decoration-none p-0 me-3">
                                {{__('general.clear_selection')}}
                            </a>

                            <div id="bulk-collections-container">
                                @livewire('cultural-object.collections', [
                                'culturalObjectIds' => [],
                                ], key('bulk-collections-action'))
                            </div>

                        </div>
                    </div>
                </div>

                {{--                <div class="col-12 mb-3">--}}
                {{--                    <div class="form-check" id="select-all-container">--}}
                {{--                        <input class="form-check-input" type="checkbox" id="select-all-checkbox">--}}
                {{--                        <label class="form-check-label" for="select-all-checkbox">--}}
                {{--                            {{__('general.select_all')}} ({{ $culturalObjects->count() }})--}}
                {{--                        </label>--}}
                {{--                    </div>--}}
                {{--                </div>--}}


                <div class="row gy-4">
                    <!-- Start object -->
                    <x-cultural-objects-list :cultural-objects="$culturalObjects" :user-likes="$user_likes"/>
                    <!-- End object -->


                </div>

            </div>



            {{ $culturalObjects->links() }}
        @endif
    </section>

@endsection


@push('scripts')
    <script>
        $(document).ready(function() {
            const livewireId = $('#bulk-collections-container').find('[wire\\:id]').attr('wire:id');
            const livewireComponent = Livewire.find(livewireId);

            function updateBulkActions() {
                const selectedIds = $('.select-object:checked').map(function() {
                    return parseInt($(this).data('id'));
                }).get();
                const totalObjects = $('.select-object').length;

                $('#selection-info-text').text(selectedIds.length + ' {{__('general.selected_objects')}}');

                    if (selectedIds.length > 0) {
                        $('#bulk-action-bar').slideDown(200);
                        $('.individual-collections-btn').hide();


                        if (livewireComponent) {
                            livewireComponent.set('culturalObjectIds', selectedIds);
                        }

                    } else {
                        $('#bulk-action-bar').slideUp(200);
                        $('.individual-collections-btn').show(); //
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
            });
    </script>
@endpush
