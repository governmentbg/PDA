<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentCallbackController;

Route::post('/payments/callback', [PaymentCallbackController::class, 'handle'])->name('api.payments.callback');
