@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <h1 class="mb-4" style="font-weight: 600; color: #333;">{{ __('cart.my_cart') }}</h1>
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if($items->isEmpty())
            <div class="alert alert-info" style="padding: 20px; border-radius: 8px;">
                {{ __('cart.empty') }}
            </div>
        @else
            <div class="cart-items mb-4">

                <div class="cart-header py-3 px-4" style="background-color: #f8f9fa; border-radius: 8px 8px 0 0; display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 20px;">
                    <div>{{ __('cart.name') }}</div>
                    <div>{{ __('cart.price') }}</div>
                    <div></div>
                </div>

                @foreach($items as $item)
                    <div class="cart-item py-3 px-4 border-bottom" style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 20px; align-items: center;">
                        <div>
                            {{-- todo: image --}}
                            <div style="font-size: 0.9em; color: #666;">
                                {{ $item->webResource->culturalObjects->first()?->title }}
                            </div>
                        </div>
                        <div>
                            <div style="">{{ number_format($item->price, 2) }} €</div>
                            <div style="">
                                {{ number_format($item->price_bgn, 2) }} {{ __('cart.bgn') }}
                            </div>
                        </div>
                        <div class="text-end">
                            <form method="POST" action="{{ route('profile.cart.remove', $item->web_resource_id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-link text-danger">
                                    <i class="fa fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="total-section mb-5 p-4" style="background-color: #f8f9fa; border-radius: 8px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style=" margin: 0;">{{ __('cart.total') }} :</h3>
                        <div style="font-size: 0.8em; color: #999;">
                            {{ __('cart.item_count', ['count' => $item_count]) }}
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 1.2em ">
                            {{ number_format($total, 2) }} € / {{ number_format($total_bgn, 2) }} {{ __('cart.bgn') }}
                        </div>
                        <div style="font-size: 0.8em; color: #999;">
                            {{ __('cart.exchange_rate', ['rate' => $exchange_rate]) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <form method="POST" action="{{ route('profile.cart.clear') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-secondary" style="border-radius: 6px; padding: 8px 16px;">
                        {{ __('cart.clear_cart') }}
                    </button>
                </form>
            </div>

            <hr style="border-top: 2px solid #eee; margin: 30px 0;">

            <div class="payment-section">
                <form method="POST" action="{{ route('profile.cart.checkout') }}">
                    @csrf
                <div class="mb-4">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="terms_digital_content" id="terms_digital_content" style="width: 18px; height: 18px; margin-right: 10px;" required>
                        <label class="form-check-label" for="terms_digital_content" style="font-size: 0.95em;">
                            {{ __('cart.terms.digital_content') }}
                        </label>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="terms_no_refund" id="terms_no_refund" style="width: 18px; height: 18px; margin-right: 10px;" required>
                        <label class="form-check-label" for="terms_no_refund" style="font-size: 0.95em;">
                            {{ __('cart.terms.no_refund') }}
                        </label>
                    </div>
                </div>

                    <button type="submit" class="btn btn-primary w-100 py-3" style="border-radius: 8px; font-weight: 600; font-size: 1.1em;">
                        {{ __('cart.generate_and_pay') }}
                    </button>
                </form>
            </div>
        @endif
    </div>

    <style>
        .form-check-input:checked {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .cart-item:hover {
            background-color: #fafafa;
        }
    </style>
@endsection
