<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class CartController extends Controller
{

    public function index()
    {
        $cartService = new CartService();
        $data = $cartService->getItemsAndTotal(auth()->user());
        return view('profile.cart', $data);
    }

    public function add($webResourceId)
    {
        try {
            $cartService = new CartService();
            $webResource = \App\Models\WebResource::findOrFail($webResourceId);
            $cartService->addItem(auth()->user(), $webResource);
            return redirect()->back()->with('success', __('messages.cart.added'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function remove($webResourceId)
    {
        $cartService = new CartService();
        $cartService->removeItem(auth()->user(), $webResourceId);
        return redirect()->back()->with('success', __('messages.cart.removed'));
    }

    public function clear()
    {
        $cartService = new CartService();
        $cartService->clearCart(auth()->user());
        return redirect()->back()->with('success', __('messages.cart.cleared'));
    }

    public function checkout(Request $request)
    {
        $termsAccepted = $request->has('terms_digital_content') && $request->has('terms_no_refund');
        $paymentService = new PaymentService();
        try {
            $payment = $paymentService->startPaymentFromCart(auth()->user(), $termsAccepted);
            $payment->load('items.webResource.culturalObjects');
            $externalPaymentUrl =  config('services.egov.payment_url');

            return view('profile.payments.show', compact('payment', 'externalPaymentUrl'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
