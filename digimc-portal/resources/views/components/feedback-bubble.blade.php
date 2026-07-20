<style>
    #feedback-bubble { position: fixed; right: 20px; bottom: 20px; z-index: 1050; }
    .feedback-btn { border-radius: 9999px; padding: 12px 16px; box-shadow: 0 8px 20px rgba(0,0,0,.15); }
    .recaptcha-invalid .g-recaptcha { outline: 2px solid #dc3545; outline-offset: 2px; border-radius: .5rem; }
</style>

{{-- Floating bubble --}}
<div id="feedback-bubble" aria-live="polite">
    <button type="button"
            id="feedbackLink"
            class="btn btn-primary feedback-btn"
            data-bs-toggle="modal"
            data-bs-target="#feedbackModal"
            aria-label="{{ __('feedback.modal.title') }}">
        <i class="fa-solid fa-comment-dots me-1" aria-hidden="true"></i>
{{--        <span>{{ __('feedback.bubble') }}</span>--}}
    </button>
</div>

{{-- Modal --}}
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content p-3">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('feedback.modal.title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('feedback.modal.buttons.close') }}"></button>
            </div>

            <div class="modal-body">
                <x-feedback-form id="feedbackForm" :modal="true" />
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function () {
            function renderRecaptcha(scopeEl) {
                const widgets = Array.from(scopeEl.querySelectorAll('.g-recaptcha'));
                if (widgets.length === 0) return;

                function renderAll() {
                    widgets.forEach(function (el) {
                        const key = el.getAttribute('data-sitekey');
                        if (!key) return;
                        if (el.querySelector('iframe')) return; // already rendered
                        try {
                            window.grecaptcha.render(el, {sitekey: key});
                        } catch (e) {
                            console.error('reCAPTCHA render failed:', e);
                        }
                    });
                }

                if (typeof window.grecaptcha === 'undefined') {
                    console.warn('reCAPTCHA API not ready yet');
                    return;
                }
                renderAll();
            }

            function attachFeedbackSubmit(scopeEl) {
                const form = scopeEl.querySelector('form[id^="feedback"]');
                if (!form) return;

                const errBox = scopeEl.querySelector('#feedbackError');
                const okBox = scopeEl.querySelector('#feedbackSuccess');
                const submitBtn = form.querySelector('button[type="submit"]');

                function clearErrors() {
                    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    form.querySelectorAll('.invalid-feedback').forEach(fb => {
                        fb.textContent = '';
                        fb.classList.remove('d-block');
                    });
                    form.querySelectorAll('[data-recaptcha-wrapper]').forEach(w => w.classList.remove('recaptcha-invalid'));
                    errBox?.classList.add('d-none');
                    okBox?.classList.add('d-none');
                }

                function showFieldError(field, messages) {
                    const msg = Array.isArray(messages) ? messages.join(' ') : String(messages || '');
                    const input = form.querySelector(`[name="${field}"]`);
                    const box = form.querySelector(`.invalid-feedback[data-error-for="${field}"]`);
                    if (input) input.classList.add('is-invalid');
                    if (box) {
                        box.textContent = msg;
                        box.classList.remove('d-none');
                        box.classList.add('d-block');
                    }
                }

                function showCaptchaError(messages) {
                    const wrap = form.querySelector('[data-recaptcha-wrapper]');
                    const box = form.querySelector('[data-error-for="g-recaptcha-response"]');
                    const msg = Array.isArray(messages) ? messages.join(' ') : String(messages || '');
                    if (wrap) wrap.classList.add('recaptcha-invalid');
                    if (box) {
                        box.textContent = msg;
                        box.classList.remove('d-none');
                        box.classList.add('d-block');
                    }
                }

                form.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    clearErrors();
                    submitBtn?.setAttribute('disabled', 'disabled');

                    try {
                        const resp = await fetch(form.action, {
                            method: 'POST',
                            headers: {'Accept': 'application/json'},
                            body: new FormData(form)
                        });

                        if (resp.ok) {
                            okBox?.classList.remove('d-none');
                            form.reset();
                            if (typeof grecaptcha !== 'undefined') grecaptcha.reset();
                            return;
                        }

                        if (resp.status === 422) {
                            const data = await resp.json();
                            const errors = data?.errors || {};
                            Object.entries(errors).forEach(([field, messages]) => {
                                if (field === 'g-recaptcha-response') showCaptchaError(messages);
                                else showFieldError(field, messages);
                            });
                            errBox?.classList.remove('d-none');
                            if (typeof grecaptcha !== 'undefined') grecaptcha.reset();
                            return;
                        }

                        const data = await resp.json().catch(() => ({}));
                        errBox && (errBox.textContent = data.message || '{{ __('feedback.modal.generic_error') }}');
                        errBox?.classList.remove('d-none');
                        if (typeof grecaptcha !== 'undefined') grecaptcha.reset();

                    } catch (ex) {
                        errBox && (errBox.textContent = '{{ __('feedback.modal.generic_error') }}');
                        errBox?.classList.remove('d-none');
                        if (typeof grecaptcha !== 'undefined') grecaptcha.reset();
                    } finally {
                        submitBtn?.removeAttribute('disabled');
                    }
                });
            }

            // Init on modal open
            document.addEventListener('shown.bs.modal', function (ev) {
                if (ev.target && ev.target.id === 'feedbackModal') {
                    renderRecaptcha(ev.target);
                    attachFeedbackSubmit(ev.target);
                }
            });

            // Init if page version exists (page form variant)
            document.addEventListener('DOMContentLoaded', function () {
                const pageForm = document.getElementById('feedbackPageForm');
                if (pageForm) {
                    renderRecaptcha(document);
                    attachFeedbackSubmit(document);
                }
            });
        })();
    </script>
@endpush
