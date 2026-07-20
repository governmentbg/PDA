<?php

namespace App\Services;

use App\Enums\PaymentStatusEnum;
use App\Enums\SettingEnum;
use App\Models\Cart;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\PurchasedWebResource;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentService
{

    public function startPaymentFromCart($user, bool $termsAccepted): Payment
    {
        if (!$termsAccepted) {
            throw new Exception(__('messages.payment.terms_not_accepted'));
        }

        $payment = $this->createFromCart($user);

        try {
            $egov = new EgovPaymentService();
            $result = $egov->createPaymentRequest($payment);

            if (empty($result['acceptedReceiptJson'])) {
                throw new Exception(__('messages.payment.egov_invalid_response'));
            }

            $payment->update([
                'payment_code'            => $result['acceptedReceiptJson']['accessCode'],
                'external_transaction_id' => $result['acceptedReceiptJson']['id'],
            ]);

        } catch (\Exception $e) {
            $payment->update(['status' => PaymentStatusEnum::SUSPENDED]);
            throw $e;
        }

        return $payment->fresh();
    }


    public function createFromCart($user): Payment
    {
        $cartService = new CartService();
        $cartData = $cartService->getItemsAndTotal($user);

        $items = $cartData['items'];
        $total = $cartData['total'];

        if ($items->isEmpty()) {
            throw new Exception(__('messages.payment.cart_empty'));
        }


        $webResourceIds = $items->pluck('web_resource_id');
        $hasPending = PaymentItem::whereIn('web_resource_id', $webResourceIds)
            ->whereHas('payment', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->whereIn('status', PaymentStatusEnum::active());
            })
            ->exists();

        if ($hasPending) {
            throw new Exception(__('messages.payment.pending_exists'));
        }

        return DB::transaction(function () use ($items, $total, $user) {
            foreach ($items as $item) {
                if (PurchasedWebResource::where('user_id', $user->id)
                    ->where('web_resource_id', $item->web_resource_id)->exists()) {
                    throw new Exception(__('messages.payment.already_purchased'));
                }
            }

            $payment = Payment::create([
                'user_id' => $user->id,
                'total_amount' => $total,
                'status' => PaymentStatusEnum::PENDING,
                'expires_at' => now()->addDays((int) SettingEnum::getValueByKeyword(SettingEnum::PAYMENT_EXPIRES_AT)),
            ]);

            foreach ($items as $item) {
                PaymentItem::create([
                    'payment_id' => $payment->id,
                    'web_resource_id' => $item->web_resource_id,
                    'price' => $item->webResource->price,
                ]);
            }

            return $payment;
        });
    }


    public function syncStatuses(): void
    {
        $egov = new EgovPaymentService();
        $days = SettingEnum::getValueByKeyword(SettingEnum::PAYMENT_SYNC_DAYS);

        Payment::where('status', PaymentStatusEnum::PENDING)
            ->whereNotNull('external_transaction_id')
            ->where('created_at', '>=', now()->subDays($days))
            ->chunkById(100, function ($payments) use ($egov) {

                $ids = $payments->pluck('external_transaction_id')->toArray();
                $result = $egov->checkPaymentStatus($ids);
                $statuses = collect($result['paymentStatuses'] ?? []);

                foreach ($payments as $payment) {
                    $paymentStatus = $statuses->firstWhere('id', $payment->external_transaction_id);

                    if (!$paymentStatus) {
                        continue;
                    }

                    $this->applyStatus($payment, $paymentStatus['status']);
                }
            });
    }


    public function markAsPaid(Payment $payment): Payment
    {
        if ($payment->status === PaymentStatusEnum::PAID) {
            return $payment;
        }

        return DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => PaymentStatusEnum::PAID,
                'paid_at' => now(),
            ]);

            $payment->load('items');
            /** @var \App\Models\PaymentItem $item */
            foreach ($payment->items as $item) {
                PurchasedWebResource::firstOrCreate(
                    [
                        'user_id' => $payment->user_id,
                        'web_resource_id' => $item->web_resource_id,
                    ],
                    [
                        'payment_id' => $payment->id,
                        'purchased_at' => now(),
                    ]
                );
            }

            $cart = Cart::where('user_id', $payment->user_id)->first();
            if ($cart) {
                $cart->delete();
            }

            return $payment;
        });
    }

    public function suspendPayment(Payment $payment): Payment
    {
        if ($payment->status !== PaymentStatusEnum::PENDING) {
            throw new Exception(__('messages.payment.cannot_suspend'));
        }

        if (!$payment->external_transaction_id) {
            throw new Exception(__('messages.payment.no_external_id'));
        }

        $egov = new EgovPaymentService();
        $result = $egov->suspendRequest($payment->external_transaction_id);

        $payment->update(['status' => PaymentStatusEnum::CANCELED]);

        return $payment->fresh();
    }

    public function paymentsUser($request){

        $query = Payment::with(['items.webResource.culturalObjects'])
            ->where('user_id', auth()->id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        $pages = SettingEnum::getValueByKeyword(SettingEnum::SETTINGS_PAGINATION_LENGTH);
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $query->orderBy($sort, $direction);

        $payments = $query->paginate($pages)->appends($request->all());

        return $payments;
    }

    public function handleCallback(array $data): void
    {
        $externalId = $data['id'] ?? null;
        $status = $data['status'] ?? null;

        if (!$externalId || !$status) {
            throw new Exception("Invalid callback data: " . json_encode($data));
        }

        $payment = Payment::where('external_transaction_id', $externalId)->first();

        if (is_null($payment)) {
            throw new Exception("Payment not found for external ID: {$externalId}");
        }

        $this->applyStatus($payment, $status);

        \Log::info("Payment {$payment->id} updated via callback to: {$status}");
    }

    public function applyStatus(Payment $payment, string $status): void
    {
        match ($status) {
            'paid'      => $this->markAsPaid($payment),
            'expired'   => $payment->update(['status' => PaymentStatusEnum::EXPIRED]),
            'canceled'  => $payment->update(['status' => PaymentStatusEnum::CANCELED]),
            'suspended' => $payment->update(['status' => PaymentStatusEnum::SUSPENDED]),
            default     => null,
        };
    }
}
