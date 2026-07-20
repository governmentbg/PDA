@php
    $current = request()->hasSession()
        ? session('locale', app()->getLocale() ?: 'bg')
        : (app()->getLocale() ?: 'bg');

    $current = in_array($current, ['en', 'bg']) ? $current : 'bg';
    $next = $current === 'bg' ? 'en' : 'bg';
    $labelNext = $next === 'bg' ? 'БГ' : 'EN';
@endphp


@if(request()->hasSession())
    <div id="language-bubble" aria-live="polite">
        {{ html()->form('POST', route('locale'))->open() }}
        @csrf
        {{ html()->hidden('locale', $next) }}
        <button type="submit"
                class="btn btn-primary language-btn"
                aria-label="{{ __('footer.language') }} (switch to {{ $labelNext }})"
                title="{{ __('footer.language') }} → {{ $labelNext }}">
            <i class="bi bi-translate me-1" aria-hidden="true"></i>
            <span class="fw-bold">{{ $labelNext }}</span>
        </button>
        {{ html()->form()->close() }}
    </div>
@endif

<style>
    #language-bubble {
        position: fixed;
        right: 132px;
        bottom: 20px;
        z-index: 10000;
    }

    .language-btn {
        border-radius: 9999px;
        padding: 12px 14px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, .15);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
</style>
