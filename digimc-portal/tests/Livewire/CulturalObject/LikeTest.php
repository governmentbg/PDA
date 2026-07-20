<?php

namespace Tests\Livewire\CulturalObject;

use App\Livewire\CulturalObject\Like;
use App\Models\CulturalObject;
use App\Models\CulturalObjectLike;
use App\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_cannot_toggle_like()
    {
        $object = CulturalObject::factory()->create();

        Livewire::test(Like::class, ['cultural_object_id' => $object->id])
            ->call('toggleLike')
            ->assertSet('liked', false);

        $this->assertDatabaseMissing('cultural_object_like', [
            'cultural_object_id' => $object->id,
        ]);
    }

    #[Test]
    public function user_can_like_an_object()
    {
        $object = CulturalObject::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(Like::class, ['cultural_object_id' => $object->id])
            ->call('toggleLike')
            ->assertSet('liked', true);

        $this->assertNotNull(CulturalObjectLike::where([
            'user_id' => $user->id,
            'cultural_object_id' => $object->id,
        ])->first());
    }

    #[Test]
    public function user_can_unlike_an_object()
    {
        $object = CulturalObject::factory()->create();
        $user = User::factory()->create();

        // seed like
        $user->likes()->create(['cultural_object_id' => $object->id]);

        $this->actingAs($user);

        Livewire::test(Like::class, ['cultural_object_id' => $object->id])
            ->call('toggleLike')
            ->assertSet('liked', false);

        $this->assertNull(CulturalObjectLike::where([
            'user_id' => $user->id,
            'cultural_object_id' => $object->id,
        ])->first());
    }

    #[Test]
    public function liked_property_is_true_if_user_has_liked()
    {
        $object = CulturalObject::factory()->create();
        $user = User::factory()->create();

        $user->likes()->create(['cultural_object_id' => $object->id]);

        $this->actingAs($user);

        Livewire::test(Like::class, ['cultural_object_id' => $object->id])
            ->assertSet('liked', true);
    }

    #[Test]
    public function liked_property_is_false_if_user_has_not_liked()
    {
        $object = CulturalObject::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(Like::class, ['cultural_object_id' => $object->id])
            ->assertSet('cultural_object_like', false);
    }
}
