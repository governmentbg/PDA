@extends('layouts.app')

@section('content')
    <section class="section services">
        <div class="container my-4">
            <h2>{{ __('gallery.my_collections') }}</h2>
            <ul class="nav nav-tabs" id="galleriesTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'my' ? 'active' : '' }}"
                            id="my-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#my"
                            type="button"
                            role="tab">
                        {{ __('gallery.my_collections') }}
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'pending' ? 'active' : '' }}"
                            id="pending-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#pending"
                            type="button"
                            role="tab">
                        {{ __('gallery.pending_collections') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'public' ? 'active' : '' }}"
                            id="public-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#public"
                            type="button"
                            role="tab">
                        {{ __('gallery.public_collections') }}
                    </button>
                </li>
            </ul>


            <div class="tab-content mt-3" id="galleriesTabContent">
                <div class="tab-pane fade {{ $activeTab === 'my' ? 'show active' : '' }}" id="my" role="tabpanel">

                    <button type="button" class="btn btn-success mb-4" id="createGalleryBtn">
                        <i class="fa fa-plus"></i> {{ __('gallery.create_button') }}
                    </button>

                    <div class="row gy-4">
                        @forelse($myGalleries as $gallery)
                            <div class="col-lg-4 col-md-6">
                                <div class="service-card d-flex flex-column h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h3 class="mb-0">
                                                <a href="{{ route('profile.galleries.show', $gallery->id) }}" class="text-decoration-none text-reset">
                                                    {{ $gallery->name }}
                                                </a>
                                            </h3>
                                            <span class="badge bg-secondary ms-2">{{ $gallery->objects_count ?? 0 }}</span>
                                        </div>
                                        @if($gallery->description)
                                            <p class="text-muted mb-0 small gallery-description">
                                                {{ Str::limit($gallery->description, 100) }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="mt-auto card-footer-actions d-flex justify-content-between align-items-center pt-2 border-top">
                                        <a href="{{ route('profile.galleries.show', $gallery->id) }}"
                                           class="btn btn-outline-primary">
                                            <i class="fa fa-eye"></i> {{ __('gallery.view_button') }}
                                        </a>
                                        <div class="btn-group d-flex align-items-center justify-content-center">
                                            <livewire:gallery.share-switch :gallery-id="$gallery->id" :status="$gallery->status"/>
                                            <button class="btn btn-outline-warning  rename-button ms-1 me-1 edit-gallery-btn" title="{{ __('gallery.rename_button') }}"
                                                    data-id="{{ $gallery->id }}"
                                                    data-name="{{ $gallery->name }}"
                                                    data-description="{{ $gallery->description ?? '' }}">
                                                <i class="fa fa-pencil"></i>
                                            </button>

                                            <form action="{{ route('profile.galleries.destroy', $gallery->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('{{ __('gallery.confirm_delete') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger"
                                                        title="{{ __('gallery.delete_button') }}">
                                                    <i class="fa fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p>{{ __('gallery.no_collections') }}</p>
                        @endforelse
                    </div>
                    <div class="mt-3">
                        {{ $myGalleries->appends(['tab' => 'my'])->links() }}
                    </div>
                </div>

                <div class="tab-pane fade {{ $activeTab === 'pending' ? 'show active' : '' }}" id="pending" role="tabpanel">
                    <div class="row gy-4">
                        @forelse($pendingGalleries as $gallery)
                            <div class="col-lg-4 col-md-6">
                                <div class="service-card d-flex flex-column h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h3 class="mb-0">
                                                <a href="{{ route('profile.galleries.show', $gallery->id) }}" class="text-decoration-none text-reset">
                                                    {{ $gallery->name }}
                                                </a>
                                            </h3>
                                            <span class="badge bg-secondary ms-2">{{ $gallery->objects_count ?? 0 }}</span>
                                        </div>
                                        @if($gallery->description)
                                            <p class="text-muted mb-0 small gallery-description">
                                                {{ Str::limit($gallery->description, 100) }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="mt-auto card-footer-actions d-flex justify-content-between align-items-center pt-2 border-top">
                                        <a href="{{ route('profile.galleries.show', $gallery->id) }}"
                                           class="btn btn-outline-primary">
                                            <i class="fa fa-eye"></i> {{ __('gallery.view_button') }}
                                        </a>
                                        <div class="btn-group">
                                            <livewire:gallery.share-switch :gallery-id="$gallery->id" :status="$gallery->status" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p>{{ __('gallery.no_pending_collections') }}</p>
                        @endforelse
                    </div>
                    <div class="mt-3">
                        {{ $pendingGalleries->appends(['tab' => 'pending'])->links() }}
                    </div>
                </div>

                <div class="tab-pane fade {{ $activeTab === 'public' ? 'show active' : '' }}" id="public" role="tabpanel">
                    <div class="row gy-4">
                        @forelse($publicGalleries as $gallery)
                            <div class="col-lg-4 col-md-6">
                                <div class="service-card d-flex flex-column h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h3 class="mb-0">
                                                <a href="{{ route('profile.galleries.show', $gallery->id) }}" class="text-decoration-none text-reset">
                                                    {{ $gallery->name }}
                                                </a>
                                            </h3>
                                            <span class="badge bg-secondary ms-2">{{ $gallery->objects_count ?? 0 }}</span>
                                        </div>
                                        @if($gallery->description)
                                            <p class="text-muted mb-0 small gallery-description">
                                                {{ Str::limit($gallery->description, 100) }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="mt-auto card-footer-actions d-flex justify-content-between align-items-center pt-2 border-top">
                                        <a href="{{ route('profile.galleries.show', $gallery->id) }}"
                                           class="btn btn-outline-primary">
                                            <i class="fa fa-eye"></i> {{ __('gallery.view_button') }}
                                        </a>
                                        <div class="btn-group">
                                            <livewire:gallery.share-switch :gallery-id="$gallery->id" :status="$gallery->status" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p>{{ __('gallery.no_public_collections') }}</p>
                        @endforelse
                    </div>
                    <div class="mt-3">
                        {{ $publicGalleries->appends(['tab' => 'public'])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="galleryForm" method="POST">
                    @csrf
                    <input type="hidden" id="_method" name="_method" value="POST">

                    <div class="modal-header">
                        <h5 class="modal-title" id="galleryModalLabel">{{ __('gallery.create_new_collection') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('gallery.close_button') }}"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('gallery.collection_name') }}</label>
                            <input type="text" name="name" id="galleryName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('gallery.collection_description') }}</label>
                            <textarea name="description" id="galleryDescription" class="form-control" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('gallery.close_button') }}</button>
                        <button type="submit" class="btn btn-primary" id="saveGalleryBtn">{{ __('gallery.save_button') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            Livewire.on('refresh-switch-button', function() {
                const url = window.location.protocol + "//" + window.location.host + window.location.pathname;
                window.location.href = url;
            });

            const modalElement = $('#galleryModal');
            const modal = new bootstrap.Modal(modalElement);

            modalElement.on('hidden.bs.modal', function () {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('overflow', '');
            });

            $('#createGalleryBtn').on('click', function() {
                $('#galleryModalLabel').text("{{ __('gallery.create_new_collection') }}");
                $('#galleryForm').attr('action', "{{ route('profile.galleries.store') }}");
                $('#_method').val('POST');
                $('#galleryName').val('');
                $('#galleryDescription').val('');
                $('#saveGalleryBtn').text("{{ __('gallery.create_button') }}");
                modal.show();
            });

            $('.edit-gallery-btn').on('click', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const description = $(this).data('description');

                $('#galleryModalLabel').text("{{ __('gallery.edit_collection') }}");
                $('#galleryForm').attr('action', "{{ route('profile.galleries.update', '') }}/" + id);
                $('#_method').val('PUT');
                $('#galleryName').val(name);
                $('#galleryDescription').val(description);
                $('#saveGalleryBtn').text("{{ __('gallery.save_button') }}");
                modal.show();
            });
        });
    </script>
@endpush
