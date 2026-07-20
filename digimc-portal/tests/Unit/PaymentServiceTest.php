<?php

namespace Tests\Unit;

use App\Enums\PaymentStatusEnum;
use App\Enums\SettingEnum;
use App\Models\Cart;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\PurchasedWebResource;
use App\Models\User;
use App\Models\WebResource;
use App\Services\CartService;
use App\Services\PaymentService;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_throws_exception_if_terms_not_accepted(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(__('messages.payment.terms_not_accepted'));

        $user = User::factory()->create();

        (new PaymentService())->startPaymentFromCart($user, false);
    }

    #[Test]
    public function it_throws_exception_if_cart_is_empty(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(__('messages.payment.cart_empty'));

        $user = User::factory()->create();

        (new PaymentService())->startPaymentFromCart($user, true);
    }

    #[Test]
    public function it_throws_exception_if_pending_payment_exists(): void
    {
        $this->expectException(Exception::class);

        $user = User::factory()->create();
        $resource = WebResource::factory()->create();

        $payment = Payment::factory()->create([
            'user_id' => $user->id,
            'status' => PaymentStatusEnum::PENDING,
        ]);

        PaymentItem::factory()->create([
            'payment_id' => $payment->id,
            'web_resource_id' => $resource->id,
        ]);

        (new CartService())->addItem($user, $resource);

        (new PaymentService())->startPaymentFromCart($user, true);
    }

    #[Test]
    public function it_throws_exception_if_resource_is_already_purchased(): void
    {
        $this->expectException(Exception::class);

        $user = User::factory()->create();
        $resource = WebResource::factory()->create();

        PurchasedWebResource::factory()->create([
            'user_id' => $user->id,
            'web_resource_id' => $resource->id,
        ]);

        (new CartService())->addItem($user, $resource);

        (new PaymentService())->startPaymentFromCart($user, true);
    }

    #[Test]
    public function it_creates_payment_and_items_and_sets_egov_data(): void
    {
        Http::fake([
            config('services.egov.create_request_url') => Http::response([
                'unacceptedReceiptJson' => null,
                'acceptedReceiptJson' => [
                    'accessCode' => 'SK1ZLB7S8H',
                    'id' => '26042311',
                    'registrationTime' => '2026-04-23T13:40:35',
                ]
            ], 200),
        ]);

        $user = User::factory()->create();
        $r1 = WebResource::factory()->create(['price' => 10]);
        $r2 = WebResource::factory()->create(['price' => 20]);

        (new CartService())->addItem($user, $r1);
        (new CartService())->addItem($user, $r2);

        $payment = (new PaymentService())->startPaymentFromCart($user, true);

        $this->assertEquals(30, $payment->total_amount);
        $this->assertEquals(PaymentStatusEnum::PENDING, $payment->status);
        $this->assertEquals('26042311', $payment->external_transaction_id);
        $this->assertEquals('SK1ZLB7S8H', $payment->payment_code);
        $this->assertNotNull($payment->expires_at);
        $this->assertDatabaseCount('payment_item', 2);
    }


    #[Test]
    public function create_from_cart_throws_if_cart_empty(): void
    {
        $this->expectException(Exception::class);

        $user = User::factory()->create();

        (new PaymentService())->createFromCart($user);
    }

    #[Test]
    public function create_from_cart_is_transactional(): void
    {
        $this->expectException(Exception::class);

        $user = User::factory()->create();
        $ok = WebResource::factory()->create(['price' => 10]);
        $bad = WebResource::factory()->create(['price' => 20]);

        PurchasedWebResource::factory()->create([
            'user_id' => $user->id,
            'web_resource_id' => $bad->id,
        ]);

        (new CartService())->addItem($user, $ok);
        (new CartService())->addItem($user, $bad);

        try {
            (new PaymentService())->createFromCart($user);
        } finally {
            $this->assertDatabaseCount('payment', 0);
            $this->assertDatabaseCount('payment_item', 0);
        }
    }

    #[Test]
    public function create_from_cart_creates_payment_and_items_with_correct_total(): void
    {
        $user = User::factory()->create();
        $r1 = WebResource::factory()->create(['price' => 15]);
        $r2 = WebResource::factory()->create(['price' => 5]);

        (new CartService())->addItem($user, $r1);
        (new CartService())->addItem($user, $r2);

        $payment = (new PaymentService())->createFromCart($user);

        $this->assertEquals(20, $payment->total_amount);
        $this->assertDatabaseCount('payment_item', 2);
    }
    #[Test]
    public function sync_marks_as_paid_if_egov_returns_paid(): void
    {
        Http::fake([
            config('services.egov.check_status_url') => Http::response([
                'paymentStatuses' => [
                    ['id' => 'EXT-1', 'status' => 'paid', 'changeTime' => now()->toIso8601String()],
                ]
            ], 200),
        ]);

        $user = User::factory()->create();
        $resource = WebResource::factory()->create();
        Cart::factory()->create(['user_id' => $user->id]);

        $payment = Payment::factory()->create([
            'user_id' => $user->id,
            'status' => PaymentStatusEnum::PENDING,
            'external_transaction_id' => 'EXT-1',
        ]);

        PaymentItem::factory()->create([
            'payment_id' => $payment->id,
            'web_resource_id' => $resource->id,
        ]);

        (new PaymentService())->syncStatuses();

        $this->assertDatabaseHas('payment', [
            'id' => $payment->id,
            'status' => PaymentStatusEnum::PAID,
        ]);
    }

    #[Test]
    public function sync_updates_status_if_expired(): void
    {
        Http::fake([
            config('services.egov.check_status_url') => Http::response([
                'paymentStatuses' => [
                    ['id' => 'EXT-2', 'status' => 'expired', 'changeTime' => now()->toIso8601String()],
                ]
            ], 200),
        ]);

        $payment = Payment::factory()->create([
            'status' => PaymentStatusEnum::PENDING,
            'external_transaction_id' => 'EXT-2',
        ]);

        (new PaymentService())->syncStatuses();

        $this->assertEquals(PaymentStatusEnum::EXPIRED, $payment->fresh()->status);
    }

    #[Test]
    public function sync_does_nothing_if_no_pending_payments(): void
    {
        Http::fake();

        Payment::factory()->create([
            'status' => PaymentStatusEnum::PAID,
            'external_transaction_id' => 'EXT-3',
        ]);

        (new PaymentService())->syncStatuses();

        Http::assertNothingSent();
    }

    #[Test]
    public function sync_does_nothing_if_status_remains_pending(): void
    {
        Http::fake([
            config('services.egov.check_status_url') => Http::response([
                'paymentStatuses' => [
                    ['id' => 'EXT-4', 'status' => 'pending', 'changeTime' => now()->toIso8601String()],
                ]
            ], 200),
        ]);

        $payment = Payment::factory()->create([
            'status' => PaymentStatusEnum::PENDING,
            'external_transaction_id' => 'EXT-4',
        ]);

        (new PaymentService())->syncStatuses();

        $this->assertEquals(PaymentStatusEnum::PENDING, $payment->fresh()->status);
    }


    #[Test]
    public function mark_as_paid_creates_purchased_and_clears_cart(): void
    {
        $user = User::factory()->create();
        $resource = WebResource::factory()->create();

        Cart::factory()->create(['user_id' => $user->id]);

        $payment = Payment::factory()->create([
            'user_id' => $user->id,
            'status' => PaymentStatusEnum::PENDING,
        ]);

        PaymentItem::factory()->create([
            'payment_id' => $payment->id,
            'web_resource_id' => $resource->id,
        ]);

        (new PaymentService())->markAsPaid($payment);

        $this->assertDatabaseHas('payment', [
            'id' => $payment->id,
            'status' => PaymentStatusEnum::PAID,
        ]);

        $this->assertDatabaseHas('purchased_web_resource', [
            'user_id' => $user->id,
            'web_resource_id' => $resource->id,
        ]);

        $this->assertDatabaseCount('cart_item', 0);
    }

    #[Test]
    public function mark_as_paid_is_idempotent(): void
    {
        $user = User::factory()->create();
        $resource = WebResource::factory()->create();

        $payment = Payment::factory()->create([
            'user_id' => $user->id,
            'status' => PaymentStatusEnum::PENDING,
        ]);

        PaymentItem::factory()->create([
            'payment_id' => $payment->id,
            'web_resource_id' => $resource->id,
        ]);

        (new PaymentService())->markAsPaid($payment);
        (new PaymentService())->markAsPaid($payment);

        $this->assertEquals(1, PurchasedWebResource::count());
    }

    #[Test]
    public function it_returns_only_payments_of_authenticated_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Payment::factory()->create(['user_id' => $user->id]);
        Payment::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user);

        $payments = (new \App\Services\PaymentService())->paymentsUser(request());

        $this->assertCount(1, $payments);
        $this->assertEquals($user->id, $payments->first()->user_id);
    }

    #[Test]
    public function it_filters_by_status(): void
    {
        $user = User::factory()->create();

        Payment::factory()->create(['user_id' => $user->id, 'status' => PaymentStatusEnum::PAID]);
        Payment::factory()->create(['user_id' => $user->id, 'status' => PaymentStatusEnum::PENDING]);

        $this->actingAs($user);

        $request = request()->merge(['status' => PaymentStatusEnum::PAID]);

        $payments = (new \App\Services\PaymentService())->paymentsUser($request);

        $this->assertCount(1, $payments);
        $this->assertEquals(PaymentStatusEnum::PAID, $payments->first()->status);
    }

    #[Test]
    public function it_filters_by_date_range(): void
    {
        $user = User::factory()->create();

        $oldPayment = Payment::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(10),
        ]);

        $recentPayment = Payment::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(2),
        ]);

        $this->actingAs($user);

        $request = request()->merge([
            'date_from' => now()->subDays(5)->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
        ]);

        $payments = (new \App\Services\PaymentService())->paymentsUser($request);

        $this->assertCount(1, $payments);
        $this->assertEquals($recentPayment->id, $payments->first()->id);
    }

    #[Test]
    public function it_sorts_by_specified_column_and_direction(): void
    {
        $user = User::factory()->create();

        $payment1 = Payment::factory()->create(['user_id' => $user->id, 'total_amount' => 50]);
        $payment2 = Payment::factory()->create(['user_id' => $user->id, 'total_amount' => 100]);

        $this->actingAs($user);

        $request = request()->merge([
            'sort' => 'total_amount',
            'direction' => 'desc'
        ]);

        $payments = (new \App\Services\PaymentService())->paymentsUser($request);

        $this->assertEquals($payment2->id, $payments->first()->id);
        $this->assertEquals($payment1->id, $payments->last()->id);
    }

    #[Test]
    public function it_returns_empty_collection_for_unauthenticated_user(): void
    {
        Payment::factory()->create(['user_id' => User::factory()->create()->id]);

        $payments = (new \App\Services\PaymentService())->paymentsUser(request());

        $this->assertCount(0, $payments);
    }

    #[Test]
    public function it_paginates_results_correctly(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $perPage = SettingEnum::getValueByKeyword(SettingEnum::SETTINGS_PAGINATION_LENGTH);

        Payment::factory()->count($perPage + 5)->create(['user_id' => $user->id]);

        $payments = (new \App\Services\PaymentService())->paymentsUser(request());

        $this->assertCount($perPage, $payments);
        $this->assertEquals($perPage + 5, $payments->total());
    }
    #[Test]
    public function it_filters_by_status_and_date_range_simultaneously(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Payment::factory()->create([
            'user_id' => $user->id,
            'status' => PaymentStatusEnum::PAID,
            'created_at' => now()->subDays(2)
        ]);

        Payment::factory()->create([
            'user_id' => $user->id,
            'status' => PaymentStatusEnum::PENDING,
            'created_at' => now()->subDays(2)
        ]);

        Payment::factory()->create([
            'user_id' => $user->id,
            'status' => PaymentStatusEnum::PAID,
            'created_at' => now()->subDays(20)
        ]);

        $request = request()->merge([
            'status' => PaymentStatusEnum::PAID,
            'date_from' => now()->subDays(5)->format('Y-m-d'),
        ]);

        $payments = (new \App\Services\PaymentService())->paymentsUser($request);

        $this->assertCount(1, $payments);
    }

    #[Test]
    public function it_returns_empty_result_when_filters_do_not_match(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Payment::factory()->create([
            'user_id' => $user->id,
            'status' => PaymentStatusEnum::PENDING
        ]);

        $request = request()->merge(['status' => PaymentStatusEnum::PAID]);

        $payments = (new \App\Services\PaymentService())->paymentsUser($request);

        $this->assertCount(0, $payments);
    }

    #[Test]
    public function it_eager_loads_required_relations(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        Payment::factory()->create(['user_id' => $user->id]);

        $payments = (new \App\Services\PaymentService())->paymentsUser(request());

        $this->assertTrue($payments->first()->relationLoaded('items'));
    }

    #[Test]
    public function it_suspends_payment_if_egov_fails(): void
    {
        Http::fake([
            config('services.egov.create_request_url') => Http::response([], 500),
        ]);

        $user = User::factory()->create();
        $r1 = WebResource::factory()->create(['price' => 10]);
        (new CartService())->addItem($user, $r1);

        $this->expectException(Exception::class);

        try {
            (new PaymentService())->startPaymentFromCart($user, true);
        } finally {
            $this->assertDatabaseHas('payment', [
                'user_id' => $user->id,
                'status' => PaymentStatusEnum::SUSPENDED,
            ]);
        }
    }

    #[Test]
    public function it_suspends_payment_successfully(): void
    {
        Http::fake([
            config('services.egov.suspend_request_url') => Http::response([
                'acceptedReceiptJson' => ['id' => 'EXT-1']
            ], 200),
        ]);

        $payment = Payment::factory()->create([
            'status' => PaymentStatusEnum::PENDING,
            'external_transaction_id' => 'EXT-1',
        ]);

        $result = (new PaymentService())->suspendPayment($payment);

        $this->assertEquals(PaymentStatusEnum::CANCELED, $result->status);
    }

    #[Test]
    public function it_throws_if_suspend_payment_not_pending(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(__('messages.payment.cannot_suspend'));

        $payment = Payment::factory()->create([
            'status' => PaymentStatusEnum::PAID,
            'external_transaction_id' => 'EXT-1',
        ]);

        (new PaymentService())->suspendPayment($payment);
    }

    #[Test]
    public function it_throws_if_suspend_payment_no_external_id(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(__('messages.payment.no_external_id'));

        $payment = Payment::factory()->create([
            'status' => PaymentStatusEnum::PENDING,
            'external_transaction_id' => null,
        ]);

        (new PaymentService())->suspendPayment($payment);
    }
}
