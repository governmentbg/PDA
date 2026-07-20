@extends('layouts.app')

@section('content')
    <div class="container py-5">

        <h1 class="mb-4 fw-semibold">
            {{ __('payments.summary') }}
        </h1>

        <div class="mb-4">
            @if($payment->isPaid())
                <div class="alert alert-success rounded-3">
                    {{ __('payments.paid') }}
                </div>
            @elseif($payment->status === \App\Enums\PaymentStatusEnum::EXPIRED)
                <div class="alert alert-danger rounded-3">
                     {{ __('payments.expired') }}
                </div>
            @else
                <div class="alert alert-warning rounded-3">
                    {{ __('payments.pending') }}

                    @if($payment->expires_at)
                        <div class="small text-danger mt-1">
                            {{ __('payments.valid_until') }}:
                            <strong>{{ $payment->expires_at->format('d.m.Y') }}</strong>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">
                <h5 class="mb-3">{{ __('payments.code') }}</h5>

                <div class="input-group align-items-center">
                    <input type="text"
                           value="{{ $payment->payment_code }}"
                           id="paymentCode"
                           class="form-control"
                           readonly>

                    <button class="btn btn-dark" onclick="copyCode()">
                        {{ __('payments.copy') }}
                    </button>
                    <span id="copyMessage" class="ms-3 text-success small" style="opacity:0;">
                        {{ __('payments.copied') }}
                    </span>
                </div>

                <small class="text-muted d-block mt-2">
                    {{ __('payments.code_hint') }}
                </small>
            </div>
        </div>

        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body p-0">

                <div class="p-3 border-bottom fw-semibold bg-light"
                     style="display:grid; grid-template-columns:2fr 1fr;">
                    <div>{{ __('payments.item') }}</div>
                    <div class="text-end">{{ __('payments.price') }}</div>
                </div>

                @foreach($payment->items as $item)
                    <div class="p-3 border-bottom"
                         style="display:grid; grid-template-columns:2fr 1fr; align-items:center;">

                        <div>
                            {{ $item->webResource->culturalObjects->first()?->title }}
                        </div>

                        <div class="text-end">
                            {{ number_format($item->price, 2) }} €
                            <div class="small text-muted">
                                {{ number_format($item->price_bgn, 2) }} {{ __('payments.bgn') }}
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body d-flex justify-content-between align-items-center">

                <strong>{{ __('payments.total') }}</strong>

                <div class="text-end">
                    <div class="fw-semibold">
                        {{ number_format($payment->total_amount, 2) }} €
                    </div>
                    <div class="small text-muted">
                        {{ number_format($payment->amount_bgn, 2) }} {{ __('payments.bgn') }}
                    </div>
                </div>

            </div>
        </div>


        @if(!$payment->isPaid() && $payment->status !== \App\Enums\PaymentStatusEnum::EXPIRED)
            <div class="text-center">
                <a href="{{ $externalPaymentUrl }}"
                   target="_blank"
                   class="btn btn-primary px-5 py-3 rounded-3 fw-semibold">

                    {{ __('payments.go_to_payment') }}
                </a>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function copyCode() {
            const input = document.getElementById('paymentCode');
            const message = document.getElementById('copyMessage');

            navigator.clipboard.writeText(input.value).then(() => {
                message.style.opacity = 1;

                setTimeout(() => {
                    message.style.opacity = 0;
                }, 2000);
            });
        }
    </script>
@endpush
