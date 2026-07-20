@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="h3 mb-3">
            {{ __('profile.title_edit') }}
        </h1>

        {{-- success flash --}}
        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        {{-- controller catch --}}
        @error('general')
        <div class="alert alert-danger">{{ $message }}</div>
        @enderror

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {!! html()->form('POST', route('profile.update') )
              ->attribute('enctype','multipart/form-data')
              ->attributes(['id' => 'profileForm', 'novalidate' => true])
              ->open() !!}

        @csrf

        @include('profile.fields', ['user' => $user])

        <div class="d-flex align-items-center justify-content-between mt-3">
            <a href="#" class="btn btn-outline-secondary" id="cancelBtn">{{ __('profile.buttons.cancel') }}</a>
            <div class="ms-auto d-flex align-items-center gap-2">
                @if($errors->has('current_password'))
                    <div class="text-danger small me-2">{{ $errors->first('current_password') }}</div>
                @endif
                {!! html()->button(__('profile.buttons.save'))->type('button')->class('btn btn-primary')->attributes(['id'=>'saveBtn']) !!}
            </div>
        </div>

        {!! html()->form()->close() !!}
    </div>

    {{-- Hidden input for current password --}}
    <input type="hidden" name="current_password" value="">

    {{-- Password confirmation modal --}}
    <div class="modal fade" id="confirmSaveModal" tabindex="-1" aria-labelledby="confirmSaveLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmSaveLabel">{{ __('profile.modal.title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>
                <div class="modal-body">
                    <p>{{ __('profile.modal.prompt') }}</p>
                    <input type="password"
                           id="current_password_visible"
                           class="form-control"
                           autocomplete="off" autocapitalize="off" autocorrect="off" spellcheck="false">
                    <div id="pwdError" class="text-danger small mt-2 d-none">{{ __('profile.modal.error') }}</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('profile.buttons.cancel') }}</button>
                    <button type="button" id="confirmSaveBtn" class="btn btn-primary">{{ __('profile.buttons.confirm') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Cancel confirmation modal --}}
    <div class="modal fade" id="confirmCancelModal" tabindex="-1" aria-labelledby="confirmCancelLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmCancelLabel">{{ __('profile.cancel_modal.title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ __('profile.cancel_modal.prompt') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('profile.cancel_modal.keep_editing') }}</button>
                    <button type="button" id="confirmCancelBtn" class="btn btn-danger">{{ __('profile.cancel_modal.discard') }}</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', function () {

            const fileInput = document.getElementById('profile_image_path');
            const fileNameInput = document.getElementById('profile_image_path_name');
            const photoPreview = document.getElementById('profile_image_preview');
            const noPhotoPlaceholder = document.getElementById('no_photo_placeholder');

            fileInput?.addEventListener('change', function() {
                const file = this.files[0];

                if (file) {
                    fileNameInput.value = file.name;

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        photoPreview.src = e.target.result;
                        photoPreview.style.display = 'block';
                        photoPreview.classList.remove('d-none')
                    }
                    reader.readAsDataURL(file);

                    noPhotoPlaceholder.classList.add('d-none')
                } else {
                    fileNameInput.value = "{{ __('profile.ui.no_file_selected') }}";
                    photoPreview.src = '';
                    photoPreview.classList.add('d-none')
                    noPhotoPlaceholder.classList.remove('d-none')
                }
            });

            const form = document.getElementById('profileForm');
            if (!form) {
                console.error('profileForm not found');
                return;
            }

            // Elements
            const saveBtn = document.getElementById('saveBtn');
            const confirmSaveBtn = document.getElementById('confirmSaveBtn');
            const modalSave = document.getElementById('confirmSaveModal');
            const pwdVis = document.getElementById('current_password_visible');
            const pwdErr = document.getElementById('pwdError');
            const pwdReal = form.querySelector('input[name="current_password"]');
            const file = form.querySelector('input[name="profile_image_path"]');

            const cancelBtn = document.getElementById('cancelBtn');
            const modalCancel = document.getElementById('confirmCancelModal');
            const confirmCancelBtn = document.getElementById('confirmCancelBtn');

            let confirmed = false;


            function showSaveModal() {
                if (window.bootstrap?.Modal) {
                    window.bootstrap.Modal.getOrCreateInstance(modalSave).show();
                } else {
                    const p = prompt("{{ __('profile.modal.prompt') }}");
                    if (!p) return;
                    pwdReal.value = p;
                    confirmed = true;
                    form.submit();
                }
            }

            form.addEventListener('submit', function (e) {
                if (confirmed) return;
                // file checks
                if (file?.files?.[0]) {
                    const f = file.files[0];
                    if (f.size > 2 * 1024 * 1024) {
                        e.preventDefault();
                        alert('Profile photo must be ≤ 2MB.');
                        return;
                    }
                    if (!['image/jpeg', 'image/png', 'image/webp'].includes(f.type)) {
                        e.preventDefault();
                        alert('Allowed image types: JPG, PNG, WebP.');
                        return;
                    }
                }
                e.preventDefault();
                showSaveModal();
            });

            // Save button
            saveBtn?.addEventListener('click', () => form.requestSubmit());

            // Confirm in modal
            confirmSaveBtn?.addEventListener('click', function() {
                if (!pwdVis.value.trim()) {
                    pwdErr.classList.remove('d-none');
                    pwdVis.focus();
                    return;
                }
                pwdReal.value = pwdVis.value;
                confirmed = true;
                window.bootstrap?.Modal.getInstance(modalSave)?.hide();
                form.submit();
            });


            cancelBtn?.addEventListener('click', function(e) {
                e.preventDefault();
                window.bootstrap?.Modal.getOrCreateInstance(modalCancel).show();
            });


            confirmCancelBtn?.addEventListener('click', function() {
                window.location.href = "{{ route('profile.show') }}";
            });
        });
    </script>

@endsection
