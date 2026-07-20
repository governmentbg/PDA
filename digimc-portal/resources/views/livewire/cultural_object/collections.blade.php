<div>

    <button type="button" wire:click="toggleModal" class="p-0 border-0 bg-transparent">
        <i class="bi bi-plus-circle"></i> {{ __('gallery.collection_button') }}
    </button>


    @if($showModal ?? false)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('gallery.choose_collections') }}</h5>
                        <button type="button" class="btn-close" wire:click="toggleModal"></button>
                    </div>

                    <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
                        <div class="row g-2 mt-3 pb-3">
                            <div class="col-8">
                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="{{ __('gallery.new_collection_placeholder') }}"
                                    wire:model.defer="newGalleryName"
                                >
                            </div>
                            <div class="col-4">
                                <button
                                    type="button"
                                    class="btn btn-primary w-100"
                                    wire:click="createNewGallery"
                                >
                                    {{ __('gallery.create_new_collection') }}
                                </button>
                            </div>
                            <div class="mt-2">
                                <textarea
                                    class="form-control"
                                    placeholder="{{ __('gallery.new_collection_description_placeholder') }}"
                                    wire:model.defer="newGalleryDescription"
                                    rows="2"
                                ></textarea>
                            </div>
                        </div>

                        @forelse($galleries as $gallery)
                            <div class="list-group mb-2">
                                <label
                                    class="list-group-item d-flex align-items-center {{ isset($selected[$gallery['id']]) && $selected[$gallery['id']] ? 'bg-success text-white' : '' }}"
                                    for="gallery_{{ $gallery['id'] }}"
                                >
                                    <input
                                        type="checkbox"
                                        class="form-check-input me-2"
                                        id="gallery_{{ $gallery['id'] }}"
                                        wire:model.live="selected.{{ $gallery['id'] }}"
                                    >
                                    {{ $gallery['name'] }}
                                </label>
                            </div>
                        @empty
                            <p class="text-muted">{{ __('gallery.no_collections_yet') }}</p>
                        @endforelse


                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="toggleModal">{{ __('gallery.close_button') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
    <style>
        .list-group-item {
            transition: background-color 0.5s ease;
        }
    </style>
    @endpush
