<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    public function index(Request $request)
    {
        try {
            $service = new PaymentService();
            $payments = $service->paymentsUser($request);

            return view('profile.payments.index', compact('payments'));
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors([$exception->getMessage()]);
        }
    }

    public function suspend(Payment $payment)
    {
        try {

            $service = new PaymentService();
            $service->suspendPayment($payment);

            return redirect()->back()->with('success', __('messages.payment.suspended_successfully'));

        } catch (\Exception $exception) {
            return redirect()->back()->withErrors([$exception->getMessage()]);
        }
    }
}
