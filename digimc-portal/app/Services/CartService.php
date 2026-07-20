<?php

namespace App\Services;

use App\Enums\PaymentStatusEnum;
use App\Enums\SettingEnum;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\PaymentItem;
use App\Models\PurchasedWebResource;
use App\Models\WebResource;
use Illuminate\Support\Facades\DB;
use Exception;

class CartService
{
    public function getCart($user): Cart
    {
        return Cart::firstOrCreate(['user_id' => $user->id]);
    }

    public function addItem($user, WebResource $webResource): CartItem
    {
        $cart = $this->getCart($user);

        if (PurchasedWebResource::where('user_id', $user->id)
            ->where('web_resource_id', $webResource->id)
            ->whereNull('deleted_at')
            ->exists()) {
            throw new Exception(__('messages.cart.already_purchased'));
        }

        if ($cart->items()->where('web_resource_id', $webResource->id)->exists()) {
            throw new Exception(__('messages.cart.already_in_cart'));
        }

        $pendingPaymentExists = PaymentItem::where('web_resource_id', $webResource->id)
            ->whereHas('payment', function($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', PaymentStatusEnum::PENDING);
            })
            ->exists();

        if ($pendingPaymentExists) {
            throw new Exception(__('messages.cart.pending_payment_exists'));
        }

        return $cart->items()->create([
            'web_resource_id' => $webResource->id,
        ]);
    }

    public function removeItem($user, int $webResourceId): bool
    {
        $cart = $this->getCart($user);

        $item = $cart->items()->where('web_resource_id', $webResourceId)->first();

        if ($item) {
            return $item->delete();
        }

        return false;
    }

    public function clearCart($user): void
    {
        $cart = $this->getCart($user);
        $cart->items->each->delete();
    }

    public function getItemsAndTotal($user): array
    {
        $cart = $this->getCart($user);

        $items = $cart->items()->with('webResource.culturalObjects')->get();

        $exchangeRate = (float) SettingEnum::getValueByKeyword(SettingEnum::EUR_TO_BGN);

        $items->transform(function ($item) use ($exchangeRate) {

            $priceEur = $item->webResource->price ?? 0;

            $item->price = (float) $priceEur;
            $item->price_bgn = round($priceEur * $exchangeRate, 2);

            return $item;
        });


        $totalEur = $items->sum('price');
        $totalBgn = $items->sum('price_bgn');

        return [
            'items' => $items,
            'item_count' => $items->count(),
            'total' => $totalEur,
            'total_bgn' => $totalBgn,
            'exchange_rate' => $exchangeRate,
        ];
    }
}
