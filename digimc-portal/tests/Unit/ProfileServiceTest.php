<?php

namespace Tests\Unit;

use App\Models\CulturalObject;
use App\Models\CulturalObjectLike;
use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProfileServiceTest extends TestCase
{
//    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', ['--database' => 'sqlite']);   // default
        $this->artisan('migrate', ['--database' => 'secondary']);  // second connection
    }


    #[Test]
    public function it_returns_paginated_user_favorites()
    {
        // setup
        $user = User::factory()->create();
        $this->actingAs($user);

        $objects = CulturalObject::factory()->count(5)->create();

        foreach ($objects as $obj) {
            $user->likes()->create(['cultural_object_id' => $obj->id]);
        }

        $service = new ProfileService();

        // code
        $paginated = $service->getUserFavoritesPaginated(5);

        // assert
        $this->assertCount(5, $paginated->items());
        $this->assertEquals(
            $objects->pluck('id')->sort()->values()->toArray(),
            collect($paginated->items())->pluck('id')->sort()->values()->toArray()
        );
    }

    #[Test]
    public function it_throws_exception_if_user_not_authenticated()
    {
        // setup
        $service = new ProfileService();

        // code
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not authenticated');

        $service->getUserFavoritesPaginated(5);
    }

    #[Test]
    public function it_adds_favorites_to_user()
    {
        // setup
        $user = User::factory()->create();
        $this->actingAs($user);

        $objects = CulturalObject::factory()->count(3)->create();

        $service = new ProfileService();

        // code
        $added = $service->addFavorites($objects->pluck('id')->toArray(), $user);

        // assert
        $this->assertEquals($objects->pluck('id')->toArray(), $added);
        $this->assertCount(3, $user->likes()->get());
    }

    #[Test]
    public function it_removes_favorites_from_user()
    {
        // setup
        $user = User::factory()->create();
        $this->actingAs($user);

        $objects = CulturalObject::factory()->count(3)->create();

        foreach ($objects as $obj) {
            $user->likes()->create(['cultural_object_id' => $obj->id]);
        }

        $service = new ProfileService();

        // code
        $removed = $service->removeFavorites([$objects[0]->id], $user);

        // assert
        $this->assertEquals([$objects[0]->id], $removed);
        $this->assertCount(2, $user->likes()->get());
    }

    #[Test]
    public function addFavorites_creates_new_likes_for_nonexistent_objects()
    {
        // setup
        $user = User::factory()->create();
        $object1 = CulturalObject::factory()->create();
        $object2 = CulturalObject::factory()->create();
        $objectIds = [$object1->id, $object2->id];
        $service = new ProfileService();

        // code
        $result = $service->addFavorites($objectIds, $user);

        // assert
        $this->assertCount(2, $user->likes);
        $this->assertTrue($user->likes->contains('cultural_object_id', $object1->id));
        $this->assertTrue($user->likes->contains('cultural_object_id', $object2->id));
    }

    #[Test]
    public function add_favorites_does_not_create_duplicate_likes()
    {
        // setup
        $user = User::factory()->create();
        $object1 = CulturalObject::factory()->create();
        $object2 = CulturalObject::factory()->create();
        $service = new ProfileService();
        CulturalObjectLike::factory()->create([
            'user_id' => $user->id,
            'cultural_object_id' => $object1->id
        ]);

        $objectIds = [$object1->id, $object2->id];

        // code
        $result = $service->addFavorites($objectIds, $user);

        // assert
        $this->assertCount(2, $user->likes);
        $this->assertEquals(1, CulturalObjectLike::where('user_id', $user->id)
            ->where('cultural_object_id', $object1->id)
            ->count());
    }

    #[Test]
    public function add_favorites_handles_empty_array()
    {
        // setup
        $user = User::factory()->create();
        $service = new ProfileService();
        $objectIds = [];

        // code
        $result = $service->addFavorites($objectIds, $user);

        // assert
        $this->assertCount(0, $user->likes);
    }

    #[Test]
    public function add_favorites_handles_single_object()
    {
        // setup
        $user = User::factory()->create();
        $object = CulturalObject::factory()->create();
        $objectIds = [$object->id];
        $service = new ProfileService();

        // code
        $result = $service->addFavorites($objectIds, $user);

        // assert
        $this->assertEquals([$object->id], $result);
        $this->assertCount(1, $user->likes);
        $this->assertEquals($object->id, $user->likes->first()->cultural_object_id);
    }

    #[Test]
    public function remove_favorites_deletes_specified_likes()
    {
        // setup
        $user = User::factory()->create();
        $object1 = CulturalObject::factory()->create();
        $object2 = CulturalObject::factory()->create();
        $object3 = CulturalObject::factory()->create();
        $service = new ProfileService();

        CulturalObjectLike::factory()->create(['user_id' => $user->id, 'cultural_object_id' => $object1->id]);
        CulturalObjectLike::factory()->create(['user_id' => $user->id, 'cultural_object_id' => $object2->id]);
        CulturalObjectLike::factory()->create(['user_id' => $user->id, 'cultural_object_id' => $object3->id]);

        $objectIdsToRemove = [$object1->id, $object2->id];

        // code
        $result = $service->removeFavorites($objectIdsToRemove, $user);

        // assert
        $this->assertCount(1, $user->fresh()->likes);
        $this->assertFalse($user->likes->contains('cultural_object_id', $object1->id));
        $this->assertFalse($user->likes->contains('cultural_object_id', $object2->id));
        $this->assertTrue($user->likes->contains('cultural_object_id', $object3->id));
    }

    #[Test]
    public function remove_favorites_handles_empty_array()
    {
        // setup
        $user = User::factory()->create();
        $object = CulturalObject::factory()->create();
        $service = new ProfileService();
        CulturalObjectLike::factory()->create([
            'user_id' => $user->id,
            'cultural_object_id' => $object->id
        ]);

        $objectIds = [];

        // code
        $result = $service->removeFavorites($objectIds, $user);

        // assert
        $this->assertCount(1, $user->fresh()->likes);
    }

    #[Test]
    public function remove_favorites_handles_nonexistent_objects_gracefully()
    {
        // setup
        $user = User::factory()->create();
        $existingObject = CulturalObject::factory()->create();
        $nonexistentObjectId = 9999;
        $service = new ProfileService();
        CulturalObjectLike::factory()->create([
            'user_id' => $user->id,
            'cultural_object_id' => $existingObject->id
        ]);

        $objectIds = [$existingObject->id, $nonexistentObjectId];

        // code
        $result = $service->removeFavorites($objectIds, $user);

        // assert
        $this->assertCount(0, $user->fresh()->likes);
    }

    #[Test]
    public function remove_favorites_does_not_affect_other_users_likes()
    {
        // setup
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $object = CulturalObject::factory()->create();
        $service = new ProfileService();
        CulturalObjectLike::factory()->create(['user_id' => $user1->id, 'cultural_object_id' => $object->id]);
        CulturalObjectLike::factory()->create(['user_id' => $user2->id, 'cultural_object_id' => $object->id]);

        $objectIds = [$object->id];

        // code
        $result = $service->removeFavorites($objectIds, $user1);

        // assert
        $this->assertEquals([$object->id], $result);
        $this->assertCount(0, $user1->fresh()->likes);
        $this->assertCount(1, $user2->fresh()->likes);
    }

}
