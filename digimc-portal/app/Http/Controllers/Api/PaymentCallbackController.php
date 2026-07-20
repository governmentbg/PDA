<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentCallbackController extends Controller
{
    public function handle(Request $request, PaymentService $service)
    {
        try {
            $service->handleCallback($request->all());

            return response()->json([
                'success' => true
            ], 200);

        } catch (\Exception $e) {
            \Log::error("eGov Callback Error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
