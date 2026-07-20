<?php

namespace Tests\Unit;

use App\Enums\GalleryEnum;
use App\Mail\GalleryApprovedMail;
use App\Mail\GalleryStatusUpdateMail;
use App\Mail\GalleryPublishRequestedMail;
use App\Models\CulturalObject;
use App\Models\Gallery;
use App\Models\GalleryCulturalObject;
use App\Models\User;
use App\Services\GalleryService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GalleryServiceTest extends TestCase
{
//    use RefreshDatabase;

//    protected $connectionsToTransact = ['sqlite', 'secondary'];

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', ['--database' => 'sqlite']);   // default
        $this->artisan('migrate', ['--database' => 'secondary']);  // second connection
    }

    #[Test]
    function user_can_create_gallery()
    {
        $user = User::factory()->create();
        \Auth::login($user);

        $request = Request::create('', '', [
            'name' => 'My Gallery',
            'status' => GalleryEnum::STATUS_PUBLIC,
        ]);

        $service = new GalleryService();
        $gallery = $service->create($request);

        $this->assertSame('My Gallery', $gallery->name);
        $this->assertSame(GalleryEnum::STATUS_PUBLIC, $gallery->status);
        $this->assertSame($user->id, $gallery->user_id);
    }

    #[Test]
    function user_can_edit_gallery()
    {
        $user = User::factory()->create();
        \Auth::login($user);

        $gallery = Gallery::factory()->create([
            'user_id' => $user->id,
            'name' => 'Old Name',
            'description' => 'Old Description',
        ]);

        $request = Request::create('', '', [
            'gallery_id' => $gallery->id,
            'name' => 'New Name',
            'description' => 'New Description',
        ]);

        $service = new GalleryService();
        $service->edit($request);

        $this->assertSame('New Name', $gallery->fresh()->name);
        $this->assertSame('New Description', $gallery->fresh()->description);
    }

    #[Test]
    function user_can_delete_gallery()
    {
        $user = User::factory()->create();
        \Auth::login($user);

        $gallery = Gallery::factory()->create(['user_id' => $user->id]);

        $request = Request::create('', '', ['gallery_id' => $gallery->id]);

        $service = new GalleryService();
        $service->delete($request);

        $this->assertSoftDeleted($gallery);
    }

    #[Test]
    function user_can_add_object_to_gallery()
    {
        $user = User::factory()->create();
        \Auth::login($user);

        $gallery = Gallery::factory()->create(['user_id' => $user->id]);
        $object = CulturalObject::factory()->create();

        $service = new GalleryService();
        $service->addObjects($gallery->id,[$object->id]);

        $this->assertTrue(GalleryCulturalObject::where('gallery_id', $gallery->id)
            ->where('cultural_object_id', $object->id)
            ->exists());
    }

    #[Test]
    function user_can_remove_object_from_gallery()
    {
        $user = User::factory()->create();
        \Auth::login($user);

        $gallery = Gallery::factory()->create(['user_id' => $user->id]);
        $object = CulturalObject::factory()->create();

        GalleryCulturalObject::create([
            'gallery_id' => $gallery->id,
            'cultural_object_id' => $object->id
        ]);

        $service = new GalleryService();
        $service->removeObjects($gallery->id,[$object->id]);

        $this->assertFalse(GalleryCulturalObject::where('gallery_id', $gallery->id)
            ->where('cultural_object_id', $object->id)
            ->exists());
    }

    #[Test]
    function list_returns_user_galleries_with_objects_in_correct_order()
    {
        $user = User::factory()->create();
        \Auth::login($user);

        $gallery = Gallery::factory()->create(['user_id' => $user->id]);
        $object1 = CulturalObject::factory()->create();
        $object2 = CulturalObject::factory()->create();
        $object3 = CulturalObject::factory()->create();


        GalleryCulturalObject::create([
            'gallery_id' => $gallery->id,
            'cultural_object_id' => $object2->id,
        ]);
        GalleryCulturalObject::create([
            'gallery_id' => $gallery->id,
            'cultural_object_id' => $object1->id,
        ]);
        GalleryCulturalObject::create([
            'gallery_id' => $gallery->id,
            'cultural_object_id' => $object3->id,
        ]);

        $service = new GalleryService();
        $lists = $service->listForUser();

        $this->assertCount(1, $lists['all']);

        $firstGallery = $lists['all']->first();
        $this->assertNotNull($firstGallery);

        $objects = $firstGallery->objects;
        $this->assertCount(3, $objects);
        $this->assertSame($object2->id, $objects[0]->id);
        $this->assertSame($object1->id, $objects[1]->id);
        $this->assertSame($object3->id, $objects[2]->id);
    }

    #[Test]
    function adding_same_object_twice_does_not_duplicate()
    {
        $user = User::factory()->create();
        \Auth::login($user);

        $gallery = Gallery::factory()->create(['user_id' => $user->id]);
        $object = CulturalObject::factory()->create();

        $request = Request::create('', '', [
            'gallery_id' => $gallery->id,
            'cultural_object_id' => $object->id
        ]);

        $service = new GalleryService();
        $service->addObjects($gallery->id,[$object->id]);
        $service->addObjects($gallery->id,[$object->id]);

        $this->assertCount(1, GalleryCulturalObject::where('gallery_id', $gallery->id)->get());
    }

    #[Test]
    function user_can_list_objects_in_gallery_in_correct_order()
    {
        $user = User::factory()->create();
        \Auth::login($user);

        $gallery = Gallery::factory()->create(['user_id' => $user->id]);

        $object1 = CulturalObject::factory()->create();
        $object2 = CulturalObject::factory()->create();
        $object3 = CulturalObject::factory()->create();

        GalleryCulturalObject::factory()->create([
            'gallery_id' => $gallery->id,
            'cultural_object_id' => $object2->id,
            'created_at' => now()->subMinutes(5),
        ]);
        GalleryCulturalObject::factory()->create([
            'gallery_id' => $gallery->id,
            'cultural_object_id' => $object1->id,
            'created_at' => now()->subMinutes(10),
        ]);
        GalleryCulturalObject::factory()->create([
            'gallery_id' => $gallery->id,
            'cultural_object_id' => $object3->id,
            'created_at' => now(),
        ]);

        $service = new GalleryService();
        $gallery_ret = $service->getGalleryWithObjects($gallery->id);

        $this->assertSame([$object1->id, $object2->id, $object3->id], $gallery_ret->objects->pluck('id')->toArray());
    }

    #[Test]
    function user_cannot_list_objects_in_gallery_of_another_user()
    {
        $user = User::factory()->create();
        \Auth::login($user);

        $otherUserGallery = Gallery::factory()->create();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Gallery not found or does not belong to user');

        $service = new GalleryService();
        $service->getGalleryWithObjects($otherUserGallery->id);
    }

    #[Test]
    function unauthenticated_user_cannot_list_objects()
    {
        $gallery = Gallery::factory()->create();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not authenticated');

        $service = new GalleryService();
        $service->getGalleryWithObjects($gallery->id);
    }

    #[Test]
    public function user_can_get_object_galleries()
    {
        $user = User::factory()->create();
        \Auth::login($user);

        $gallery1 = Gallery::factory()->create(['user_id' => $user->id]);
        $gallery2 = Gallery::factory()->create(['user_id' => $user->id]);

        $objectId = 1;

        GalleryCulturalObject::factory()->create([
            'gallery_id' => $gallery1->id,
            'cultural_object_id' => $objectId,
        ]);
        GalleryCulturalObject::factory()->create([
            'gallery_id' => $gallery2->id,
            'cultural_object_id' => $objectId,
        ]);

        $service = new GalleryService();
        $galleries = $service->getObjectGalleries($objectId);

        $this->assertCount(2, $galleries);
        $this->assertTrue($galleries->pluck('id')->contains($gallery1->id));
        $this->assertTrue($galleries->pluck('id')->contains($gallery2->id));
    }

    #[Test]
    public function unauthenticated_user_cannot_get_object_galleries()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not authenticated');

        $service = new GalleryService();
        $service->getObjectGalleries(1);
    }

    #[Test]
    public function owner_can_toggle_gallery_from_private_to_for_review()
    {
        Mail::fake();

        $service = new GalleryService();
        $user = User::factory()->create();
        \Auth::login($user);

        $gallery = Gallery::factory()->create([
            'user_id' => $user->id,
            'status' => GalleryEnum::STATUS_PRIVATE
        ]);

        GalleryCulturalObject::factory()->create(['gallery_id' => $gallery->id]);

        $result = $service->toggleShare($gallery->id, true);

        $this->assertEquals(GalleryEnum::STATUS_PENDING, $result);
        $this->assertEquals(GalleryEnum::STATUS_PENDING, $gallery->fresh()->status);

        Mail::assertSent(GalleryPublishRequestedMail::class, function ($mail) use ($user, $gallery) {
            return $mail->hasTo($user->email)
                && $mail->gallery->id === $gallery->id;
        });
    }

    #[Test]
    public function owner_can_toggle_gallery_from_for_review_to_private()
    {
        $service = new GalleryService();
        $user = User::factory()->create();
        \Auth::login($user);

        $gallery = Gallery::factory()->create([
            'user_id' => $user->id,
            'status' => GalleryEnum::STATUS_PENDING
        ]);

        $result = $service->toggleShare($gallery->id, false);

        $this->assertEquals(GalleryEnum::STATUS_PRIVATE, $result);
        $this->assertEquals(GalleryEnum::STATUS_PRIVATE, $gallery->fresh()->status);
    }

    #[Test]
    public function owner_can_toggle_gallery_from_public_to_private()
    {
        $service = new GalleryService();
        $user = User::factory()->create();
        \Auth::login($user);

        $gallery = Gallery::factory()->create([
            'user_id' => $user->id,
            'status' => GalleryEnum::STATUS_PUBLIC
        ]);

        $result = $service->toggleShare($gallery->id, false);

        $this->assertEquals(GalleryEnum::STATUS_PRIVATE, $result);
        $this->assertEquals(GalleryEnum::STATUS_PRIVATE, $gallery->fresh()->status);
    }
    #[Test]
    public function unauthenticated_user_cannot_toggle_gallery()
    {
        $service = new GalleryService();
        $gallery = Gallery::factory()->create(['status' => GalleryEnum::STATUS_PRIVATE]);

        $result = $service->toggleShare($gallery->id, true);

        $this->assertFalse($result);
        $this->assertEquals(GalleryEnum::STATUS_PRIVATE, $gallery->fresh()->status);
    }

    #[Test]
    public function user_cannot_toggle_other_users_gallery()
    {
        $service = new GalleryService();
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        \Auth::login($otherUser);

        $gallery = Gallery::factory()->create([
            'user_id' => $owner->id,
            'status' => GalleryEnum::STATUS_PRIVATE
        ]);

        $result = $service->toggleShare($gallery->id, true);

        $this->assertFalse($result);
        $this->assertEquals(GalleryEnum::STATUS_PRIVATE, $gallery->fresh()->status);
    }

    #[Test]
    public function toggle_on_a_public_gallery_returns_it_to_private()
    {
        $service = new GalleryService();
        $user = User::factory()->create();
        \Auth::login($user);

        $gallery = Gallery::factory()->create([
            'user_id' => $user->id,
            'status' => GalleryEnum::STATUS_PUBLIC
        ]);

        $result = $service->toggleShare($gallery->id, true);

        $this->assertEquals(GalleryEnum::STATUS_PRIVATE, $result);
        $this->assertEquals(GalleryEnum::STATUS_PRIVATE, $gallery->fresh()->status);
    }

    #[Test]
    function list_pending_returns_only_pending_galleries()
    {
        $user = User::factory()->create();
        \Auth::login($user);

        $pendingGallery = Gallery::factory()->create(['status' => GalleryEnum::STATUS_PENDING]);
        $privateGallery = Gallery::factory()->create(['status' => GalleryEnum::STATUS_PRIVATE]);

        $service = new GalleryService();
        $result = $service->listPending();

        $this->assertTrue($result->pluck('id')->contains($pendingGallery->id));
        $this->assertFalse($result->pluck('id')->contains($privateGallery->id));
    }

    #[Test]
    function list_public_returns_only_public_galleries()
    {
        $user = User::factory()->create();
        \Auth::login($user);

        Gallery::factory()->create(['status' => GalleryEnum::STATUS_PUBLIC, 'created_at' => now()->addYear(),]);
        Gallery::factory()->create(['status' => GalleryEnum::STATUS_PENDING, 'created_at' => now()->addYear(),]);

        $service = new GalleryService();
        $result = $service->listPublic();

        $statuses = $result->getCollection()->pluck('status');

        $this->assertNotEmpty($statuses);
        $this->assertTrue($statuses->every(fn ($status) => $status === GalleryEnum::STATUS_PUBLIC));
    }

    #[Test]
    function approve_changes_gallery_status_to_public()
    {
        Mail::fake();
        /** @var \App\Models\Gallery $gallery */
        $gallery = Gallery::factory()->create(['status' => GalleryEnum::STATUS_PENDING]);

        $service = new GalleryService();
        $service->approve($gallery);

        $this->assertEquals(GalleryEnum::STATUS_PUBLIC, $gallery->fresh()->status);
    }

    #[Test]
    function reject_changes_gallery_status_to_private()
    {
        Mail::fake();
        /** @var \App\Models\Gallery $gallery */
        $gallery = Gallery::factory()->create(['status' => GalleryEnum::STATUS_PENDING, 'rejection_reason' => 'test']);

        $service = new GalleryService();
        $service->setPrivate($gallery,$gallery->rejection_reason);

        $this->assertEquals(GalleryEnum::STATUS_PRIVATE, $gallery->fresh()->status);
    }

    #[Test]
    function unauthenticated_user_cannot_list_for_user()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not authenticated');

        $service = new GalleryService();
        $service->listForUser();
    }

    #[Test]
    function unauthenticated_user_cannot_create_gallery()
    {
        $request = Request::create('', '', ['name' => 'Test Gallery']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not authenticated');

        $service = new GalleryService();
        $service->create($request);
    }

    #[Test]
    function user_cannot_edit_nonexistent_gallery()
    {
        $user = User::factory()->create();
        \Auth::login($user);

        $request = Request::create('', '', ['gallery_id' => 999, 'name' => 'New Name']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Gallery not found');

        $service = new GalleryService();
        $service->edit($request);
    }

    #[Test]
    function user_cannot_edit_other_users_gallery()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        \Auth::login($user);

        $gallery = Gallery::factory()->create(['user_id' => $otherUser->id]);

        $request = Request::create('', '', ['gallery_id' => $gallery->id, 'name' => 'Hack']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Gallery does not belong to user');

        $service = new GalleryService();
        $service->edit($request);
    }

    #[Test]
    function user_cannot_delete_nonexistent_gallery()
    {
        $user = User::factory()->create();
        \Auth::login($user);

        $request = Request::create('', '', ['gallery_id' => 999]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Gallery not found');

        $service = new GalleryService();
        $service->delete($request);
    }

    #[Test]
    function user_cannot_delete_other_users_gallery()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        \Auth::login($user);

        $gallery = Gallery::factory()->create(['user_id' => $otherUser->id]);

        $request = Request::create('', '', ['gallery_id' => $gallery->id]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Gallery does not belong to user');

        $service = new GalleryService();
        $service->delete($request);
    }

    #[Test]
    function unauthenticated_user_cannot_add_object()
    {
        $request = Request::create('', '', ['gallery_id' => 1, 'cultural_object_id' => 1]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not authenticated');

        $service = new GalleryService();
        $service->addObjects(1,[1]);
    }

    #[Test]
    function user_cannot_add_object_to_nonexistent_gallery()
    {
        $user = User::factory()->create();
        \Auth::login($user);

        $object = CulturalObject::factory()->create();

        $request = Request::create('', '', ['gallery_id' => 999, 'cultural_object_id' => $object->id]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Gallery not found');

        $service = new GalleryService();
        $service->addObjects(0,[$object->id]);
    }

    #[Test]
    function user_cannot_add_nonexistent_object()
    {
        $user = User::factory()->create();
        \Auth::login($user);

        $gallery = Gallery::factory()->create(['user_id' => $user->id]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cultural objects not found');

        $service = new GalleryService();
        $service->addObjects($gallery->id,[999]);
    }

    #[Test]
    function unauthenticated_user_cannot_remove_object()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not authenticated');

        $service = new GalleryService();
        $service->removeObjects(1,[1]);
    }

    #[Test]
    function user_cannot_remove_object_from_nonexistent_gallery()
    {
        $user = User::factory()->create();
        \Auth::login($user);

        $object = CulturalObject::factory()->create();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Gallery not found');

        $service = new GalleryService();
        $service->removeObjects(999,[$object->id]);
    }

    #[Test]
    function user_cannot_remove_nonexistent_object()
    {
        $user = User::factory()->create();
        \Auth::login($user);

        $gallery = Gallery::factory()->create(['user_id' => $user->id]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cultural objects not found');

        $service = new GalleryService();
        $service->removeObjects($gallery->id,[999]);
    }

    #[Test]
    function get_public_gallery_with_objects_returns_gallery_and_objects()
    {
        $user = User::factory()->create();
        $gallery = Gallery::factory()->create([
            'user_id' => $user->id,
            'status' => GalleryEnum::STATUS_PUBLIC,
        ]);

        $object1 = CulturalObject::factory()->create();
        $object2 = CulturalObject::factory()->create();

        GalleryCulturalObject::create([
            'gallery_id' => $gallery->id,
            'cultural_object_id' => $object1->id,
            'created_at' => now()->subMinutes(5),
        ]);
        GalleryCulturalObject::create([
            'gallery_id' => $gallery->id,
            'cultural_object_id' => $object2->id,
            'created_at' => now(),
        ]);

        $service = new GalleryService();
        $result = $service->getPublicGalleryWithObjects($gallery->id);

        $this->assertSame($gallery->id, $result->id);
        $this->assertSame($user->id, $result->user->id);
        $this->assertCount(2, $result->objects);
        $this->assertSame([$object1->id, $object2->id], $result->objects->pluck('id')->toArray());
    }

    #[Test]
    function get_public_gallery_with_objects_throws_exception_if_not_public()
    {
        $gallery = Gallery::factory()->create([
            'status' => GalleryEnum::STATUS_PRIVATE,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Gallery not found or not public');

        $service = new GalleryService();
        $service->getPublicGalleryWithObjects($gallery->id);
    }

    #[Test]
    function approve_sets_status_to_public_and_published_at()
    {
        /** @var \App\Models\Gallery $gallery */
        $gallery = Gallery::factory()->create([
            'status' => GalleryEnum::STATUS_PENDING,
            'published_at' => null
        ]);

        $service = new GalleryService();
        $service->approve($gallery);

        $gallery->refresh();

        $this->assertEquals(GalleryEnum::STATUS_PUBLIC, $gallery->status);
        $this->assertNotNull($gallery->published_at);
    }

    #[Test]
    function approve_sends_email_to_gallery_owner()
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'test_approve_' . uniqid() . '@example.com',]);
        /** @var \App\Models\Gallery $gallery */
        $gallery = Gallery::factory()->create([
            'status' => GalleryEnum::STATUS_PENDING,
            'user_id' => $user->id
        ]);

        $service = new GalleryService();
        $service->approve($gallery);

        Mail::assertSent(GalleryApprovedMail::class, function ($mail) use ($user, $gallery) {
            return $mail->hasTo($user->email) &&
                $mail->gallery->id === $gallery->id;
        });
    }
    #[Test]
    function approve_sends_approval_email()
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'test_approve_' . uniqid() . '@example.com',]);
        /** @var \App\Models\Gallery $gallery */
        $gallery = Gallery::factory()->create([
            'user_id' => $user->id,
            'status' => GalleryEnum::STATUS_PENDING
        ]);

        $service = new GalleryService();
        $service->approve($gallery);

        Mail::assertSent(GalleryApprovedMail::class, function ($mail) use ($user, $gallery) {
            return $mail->hasTo($user->email) && $mail->gallery->is($gallery);
        });
    }

    #[Test]
    function set_private_from_pending_sets_status_to_private_reason_and_sends_reject_email()
    {
        Mail::fake();

        $rejectionReason = 'Not enough content.';
        $user = User::factory()->create(['email' => 'test_reject_' . uniqid() . '@example.com',]);
        /** @var \App\Models\Gallery $gallery */
        $gallery = Gallery::factory()->create([
            'user_id' => $user->id,
            'status' => GalleryEnum::STATUS_PENDING,
            'rejection_reason' => null,
        ]);

        $service = new GalleryService();
        $service->setPrivate($gallery, $rejectionReason);

        $freshGallery = $gallery->fresh();

        $this->assertEquals(GalleryEnum::STATUS_PRIVATE, $freshGallery->status);
        $this->assertEquals($rejectionReason, $freshGallery->rejection_reason);

        Mail::assertSent(GalleryStatusUpdateMail::class, function ($mail) use ($user, $gallery, $rejectionReason) {
            return $mail->hasTo($user->email)
                && $mail->gallery->is($gallery)
                && $mail->reason === $rejectionReason
                && $mail->actionType === 'reject';
        });
    }

    #[Test]
    function set_private_from_public_sets_status_to_private_reason_and_sends_unpublish_email()
    {
        Mail::fake();

        $reason = 'Unpublished by admin due to violation.';
        $user = User::factory()->create(['email' => 'test_unpublished_' . uniqid() . '@example.com',]);
        /** @var \App\Models\Gallery $gallery */
        $gallery = Gallery::factory()->create([
            'user_id' => $user->id,
            'status' => GalleryEnum::STATUS_PUBLIC,
            'rejection_reason' => null,
        ]);

        $service = new GalleryService();
        $service->setPrivate($gallery, $reason);

        $freshGallery = $gallery->fresh();

        $this->assertEquals(GalleryEnum::STATUS_PRIVATE, $freshGallery->status);
        $this->assertEquals($reason, $freshGallery->rejection_reason);

        Mail::assertSent(GalleryStatusUpdateMail::class, function ($mail) use ($user, $gallery, $reason) {
            return $mail->hasTo($user->email)
                && $mail->gallery->is($gallery)
                && $mail->reason === $reason
                && $mail->actionType === 'unpublish';
        });
    }

    #[Test]
    public function latest_public_collections_picks_first_thumbnail_in_collection_order()
    {
        // Arrange
        $gallery = Gallery::factory()->create([
            'status' => GalleryEnum::STATUS_PUBLIC,
        ]);

        $object1 = CulturalObject::factory()->create(['thumbnail_url' => '']);
        $object2 = CulturalObject::factory()->create(['thumbnail_url' => 'https://example.test/thumb-2.jpg']);

        GalleryCulturalObject::factory()->create([
            'gallery_id' => $gallery->id,
            'cultural_object_id' => $object1->id,
            'created_at' => now()->subMinute(),
        ]);
        GalleryCulturalObject::factory()->create([
            'gallery_id' => $gallery->id,
            'cultural_object_id' => $object2->id,
            'created_at' => now(),
        ]);

        $service = new GalleryService();

        // Act
        $response = $service->latestPublicCollections(6);

        // Assert
        $gallery = $response->firstWhere('id', $gallery->id);
        $this->assertNotNull($gallery);
        $this->assertSame('https://example.test/thumb-2.jpg', $gallery->preview_thumbnail_url);
    }

    #[Test]
    public function latest_public_collections_returns_only_public_and_respects_limit()
    {
        // Arrange
        Gallery::factory()->count(3)->create(['status' => GalleryEnum::STATUS_PUBLIC]);
        Gallery::factory()->count(2)->create(['status' => GalleryEnum::STATUS_PRIVATE]);

        $service = new GalleryService();

        // Act
        $response = $service->latestPublicCollections(2);

        // Assert
        $this->assertCount(2, $response);
    }

    #[Test]
    public function owner_cannot_request_publication_for_empty_gallery()
    {
        // Arrange
        Mail::fake();

        $service = new GalleryService();

        $user = User::factory()->create();
        \Auth::login($user);

        $gallery = Gallery::factory()->create([
            'user_id' => $user->id,
            'status' => GalleryEnum::STATUS_PRIVATE,
        ]);

        // Act
        $result = $service->toggleShare($gallery->id, true);

        // Assert
        $this->assertFalse($result);
        $this->assertEquals(GalleryEnum::STATUS_PRIVATE, $gallery->fresh()->status);

        Mail::assertNotSent(GalleryPublishRequestedMail::class);
    }
}
