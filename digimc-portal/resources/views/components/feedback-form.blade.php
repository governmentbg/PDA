<form id="{{ $id ?? 'feedbackForm' }}" method="POST" action="{{ route('feedback.store') }}" novalidate>
    @csrf

    @include('feedback.fields', ['limits' => $limits, 'categories' => $categories])

    {{-- reCAPTCHA --}}
    @if(!empty($recaptchaSiteKey))
    <div class="mb-3" data-recaptcha-wrapper>
        <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
        <div class="invalid-feedback d-none" data-error-for="g-recaptcha-response"></div>
    </div>
    @endif

    <div id="feedbackSuccess" class="alert alert-success d-none mt-3" role="alert">
        {{ __('feedback.modal.success') }}
    </div>
    <div id="feedbackError" class="alert alert-danger d-none mt-3" role="alert">
        {{ __('feedback.modal.generic_error') }}
    </div>

    <div class="d-flex justify-content-end gap-2">
        @if($modal ?? false)
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                {{ __('feedback.modal.buttons.close') }}
            </button>
        @endif
        <button type="submit" class="btn btn-primary">
            {{ __('feedback.modal.buttons.send') }}
        </button>
    </div>
</form>
