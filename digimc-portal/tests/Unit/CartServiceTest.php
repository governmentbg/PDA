<?php

namespace Tests\Unit;

use App\Enums\PaymentStatusEnum;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\PurchasedWebResource;
use App\Models\User;
use App\Models\WebResource;
use App\Services\CartService;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function it_gets_an_existing_cart_or_creates_a_new_one(): void
    {
        $service = new CartService();
        $user = User::factory()->create();

        $cart = $service->getCart($user);

        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertEquals($user->id, $cart->user_id);
        $this->assertDatabaseHas('cart', ['user_id' => $user->id]);
    }

    #[Test]
    public function it_creates_new_cart_when_none_exists(): void
    {
        $service = new CartService();
        $user = User::factory()->create();

        $this->assertDatabaseMissing('cart', ['user_id' => $user->id]);

        $cart = $service->getCart($user);

        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertDatabaseHas('cart', [
            'user_id' => $user->id,
            'id' => $cart->id
        ]);
    }

    #[Test]
    public function it_returns_existing_cart_when_one_exists(): void
    {
        $service = new CartService();
        $user = User::factory()->create();

        $existingCart = Cart::factory()->create(['user_id' => $user->id]);

        $retrievedCart = $service->getCart($user);

        $this->assertEquals($existingCart->id, $retrievedCart->id);
        $this->assertCount(1, Cart::where('user_id', $user->id)->get());
    }

    #[Test]
    public function it_successfully_adds_an_item_to_the_cart(): void
    {
        $service = new CartService();
        $user = User::factory()->create();
        $webResource = WebResource::factory()->create();

        $cartItem = $service->addItem($user, $webResource);

        $this->assertInstanceOf(CartItem::class, $cartItem);
        $this->assertEquals($webResource->id, $cartItem->web_resource_id);
        $this->assertDatabaseHas('cart_item', [
            'web_resource_id' => $webResource->id,
            'cart_id' => $cartItem->cart_id
        ]);
    }

    #[Test]
    public function it_throws_exception_if_resource_is_already_purchased(): void
    {
        $service = new CartService();
        $user = User::factory()->create();
        $webResource = WebResource::factory()->create();

        PurchasedWebResource::factory()->create([
            'user_id' => $user->id,
            'web_resource_id' => $webResource->id,
            'deleted_at' => null,
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(__('messages.cart.already_purchased'));

        $service->addItem($user, $webResource);
    }

    #[Test]
    public function it_does_not_throw_exception_for_deleted_purchased_resource(): void
    {
        $service = new CartService();
        $user = User::factory()->create();
        $webResource = WebResource::factory()->create();

        PurchasedWebResource::factory()->create([
            'user_id' => $user->id,
            'web_resource_id' => $webResource->id,
            'deleted_at' => now(),
        ]);

        $cartItem = $service->addItem($user, $webResource);

        $this->assertInstanceOf(CartItem::class, $cartItem);
        $this->assertEquals($webResource->id, $cartItem->web_resource_id);
    }

    #[Test]
    public function it_throws_exception_if_item_is_already_in_cart(): void
    {
        $service = new CartService();
        $user = User::factory()->create();
        $webResource = WebResource::factory()->create();

        $service->addItem($user, $webResource);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(__('messages.cart.already_in_cart'));

        $service->addItem($user, $webResource);
    }

    #[Test]
    public function it_throws_exception_if_there_is_a_pending_payment_for_the_resource(): void
    {
        $service = new CartService();
        $user = User::factory()->create();
        $webResource = WebResource::factory()->create();

        $payment = Payment::factory()->create([
            'user_id' => $user->id,
            'status' => PaymentStatusEnum::PENDING,
        ]);

        PaymentItem::factory()->create([
            'payment_id' => $payment->id,
            'web_resource_id' => $webResource->id,
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(__('messages.cart.pending_payment_exists'));

        $service->addItem($user, $webResource);
    }

    #[Test]
    public function it_allows_adding_if_payment_is_not_pending(): void
    {
        $service = new CartService();
        $user = User::factory()->create();
        $webResource = WebResource::factory()->create();

        $payment = Payment::factory()->create([
            'user_id' => $user->id,
            'status' => PaymentStatusEnum::PAID,
        ]);

        PaymentItem::factory()->create([
            'payment_id' => $payment->id,
            'web_resource_id' => $webResource->id,
        ]);

        $cartItem = $service->addItem($user, $webResource);

        $this->assertInstanceOf(CartItem::class, $cartItem);
        $this->assertEquals($webResource->id, $cartItem->web_resource_id);
    }

    #[Test]
    public function it_removes_an_item_from_the_cart(): void
    {
        $service = new CartService();
        $user = User::factory()->create();
        $webResource = WebResource::factory()->create();

        $service->addItem($user, $webResource);
        $result = $service->removeItem($user, $webResource->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('cart_item', [
            'web_resource_id' => $webResource->id
        ]);
    }

    #[Test]
    public function it_returns_false_when_removing_nonexistent_item(): void
    {
        $service = new CartService();
        $user = User::factory()->create();
        $webResource = WebResource::factory()->create();

        $result = $service->removeItem($user, $webResource->id);

        $this->assertFalse($result);
    }

    #[Test]
    public function it_clears_the_entire_cart(): void
    {
        $service = new CartService();
        $user = User::factory()->create();

        $webResources = WebResource::factory()->count(3)->create();

        foreach ($webResources as $webResource) {
            $service->addItem($user, $webResource);
        }

        $service->clearCart($user);

        $cart = $service->getCart($user);
        $this->assertEquals(0, $cart->items()->count());
    }

    #[Test]
    public function it_clears_empty_cart_without_errors(): void
    {
        $service = new CartService();
        $user = User::factory()->create();

        $this->expectNotToPerformAssertions();

        $service->clearCart($user);
    }

    #[Test]
    public function it_returns_items_and_calculates_total_price_correctly(): void
    {
        $service = new CartService();
        $user = User::factory()->create();

        $res1 = WebResource::factory()->create(['price' => 10.00]);
        $res2 = WebResource::factory()->create(['price' => 25.50]);

        $service->addItem($user, $res1);
        $service->addItem($user, $res2);

        $result = $service->getItemsAndTotal($user);

        $this->assertCount(2, $result['items']);
        $this->assertEquals(35.50, $result['total']);
        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('total', $result);
    }

    #[Test]
    public function it_returns_empty_items_and_zero_total_for_empty_cart(): void
    {
        $service = new CartService();
        $user = User::factory()->create();

        $result = $service->getItemsAndTotal($user);

        $this->assertCount(0, $result['items']);
        $this->assertEquals(0, $result['total']);
    }

    #[Test]
    public function it_can_add_multiple_different_items_to_cart(): void
    {
        $service = new CartService();
        $user = User::factory()->create();

        $webResources = WebResource::factory()->count(5)->create();

        foreach ($webResources as $webResource) {
            $cartItem = $service->addItem($user, $webResource);
            $this->assertInstanceOf(CartItem::class, $cartItem);
        }

        $cart = $service->getCart($user);
        $this->assertEquals(5, $cart->items()->count());
    }

    #[Test]
    public function it_removes_only_specific_item_not_all_items(): void
    {
        $service = new CartService();
        $user = User::factory()->create();

        $webResources = WebResource::factory()->count(3)->create();

        foreach ($webResources as $webResource) {
            $service->addItem($user, $webResource);
        }


        $service->removeItem($user, $webResources[0]->id);

        $cart = $service->getCart($user);
        $this->assertEquals(2, $cart->items()->count());
        $this->assertDatabaseMissing('cart_item', ['web_resource_id' => $webResources[0]->id]);
        $this->assertDatabaseHas('cart_item', ['web_resource_id' => $webResources[1]->id]);
        $this->assertDatabaseHas('cart_item', ['web_resource_id' => $webResources[2]->id]);
    }

    #[Test]
    public function it_handles_concurrent_cart_access(): void
    {
        $service = new CartService();
        $user = User::factory()->create();
        $webResource = WebResource::factory()->create();

        $cart1 = $service->getCart($user);

        $cart2 = $service->getCart($user);

        $this->assertEquals($cart1->id, $cart2->id);
        $this->assertSame($cart1->fresh()->toArray(), $cart2->fresh()->toArray());
    }

    #[Test]
    public function it_does_not_affect_other_users_carts(): void
    {
        $service = new CartService();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $webResource = WebResource::factory()->create();

        $service->addItem($user1, $webResource);

        $result2 = $service->getItemsAndTotal($user2);
        $this->assertCount(0, $result2['items']);
        $this->assertEquals(0, $result2['total']);

        $result1 = $service->getItemsAndTotal($user1);
        $this->assertCount(1, $result1['items']);
    }

    #[Test]
    public function it_handles_decimal_prices_correctly(): void
    {
        $service = new CartService();
        $user = User::factory()->create();

        $res1 = WebResource::factory()->create(['price' => 10.99]);
        $res2 = WebResource::factory()->create(['price' => 25.01]);

        $service->addItem($user, $res1);
        $service->addItem($user, $res2);

        $result = $service->getItemsAndTotal($user);

        $this->assertEquals(36.00, $result['total']);
    }

    #[Test]
    public function it_preserves_cart_after_user_logout_login(): void
    {
        $service = new CartService();
        $user = User::factory()->create();
        $webResource = WebResource::factory()->create();

        $service->addItem($user, $webResource);

        $sameUser = User::find($user->id);

        $result = $service->getItemsAndTotal($sameUser);

        $this->assertCount(1, $result['items']);
        $this->assertEquals($webResource->price, $result['total']);
    }

    #[Test]
    public function it_works_with_large_number_of_items(): void
    {
        $service = new CartService();
        $user = User::factory()->create();

        $webResources = WebResource::factory()->count(100)->create();

        foreach ($webResources as $webResource) {
            $service->addItem($user, $webResource);
        }

        $cart = $service->getCart($user);
        $this->assertEquals(100, $cart->items()->count());
    }

}
