<?php

namespace Tests\Livewire\Gallery;

use App\Livewire\Gallery\ShareSwitch;
use App\Models\User;
use App\Models\Gallery;
use App\Enums\GalleryEnum;
use App\Services\GalleryService;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class ShareSwitchTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function component_is_enabled_if_gallery_status_is_public()
    {
        $user = User::factory()->create();
        $gallery = Gallery::factory()->create(['user_id' => $user->id, 'status' => GalleryEnum::STATUS_PUBLIC]);

        $component = Livewire::test(ShareSwitch::class, ['galleryId' => $gallery->id, 'status' => $gallery->status]);

        $component->assertSet('enabled', true);
    }

    #[Test]
    public function component_is_enabled_if_gallery_status_is_for_review()
    {
        $user = User::factory()->create();
        $gallery = Gallery::factory()->create(['user_id' => $user->id, 'status' => GalleryEnum::STATUS_PENDING]);

        $component = Livewire::test(ShareSwitch::class, ['galleryId' => $gallery->id, 'status' => $gallery->status]);

        $component->assertSet('enabled', true);
    }

    #[Test]
    public function component_is_disabled_if_gallery_status_is_private()
    {
        $user = User::factory()->create();
        $gallery = Gallery::factory()->create(['user_id' => $user->id, 'status' => GalleryEnum::STATUS_PRIVATE]);

        $component = Livewire::test(ShareSwitch::class, ['galleryId' => $gallery->id, 'status' => $gallery->status]);

        $component->assertSet('enabled', false);
    }

    #[Test]
    public function toggle_method_inverts_the_enabled_property()
    {
        $user = User::factory()->create();
        $gallery = Gallery::factory()->create(['user_id' => $user->id, 'status' => GalleryEnum::STATUS_PRIVATE]);

        $component = Livewire::test(ShareSwitch::class, ['galleryId' => $gallery->id, 'status' => $gallery->status]);

        $component->assertSet('enabled', false);

        $component->set('enabled', true);

        $component->call('toggle');

        $component->assertSet('enabled', true);
    }

    #[Test]
    public function toggle_method_calls_service_with_correct_parameters()
    {
        $serviceMock = $this->mock(GalleryService::class);
        $user = User::factory()->create();
        Auth::login($user);

        $gallery = Gallery::factory()->create(['user_id' => $user->id, 'status' => GalleryEnum::STATUS_PRIVATE]);

        $component = Livewire::test(ShareSwitch::class, ['galleryId' => $gallery->id, 'status' => $gallery->status]);

        $this->assertEquals(false, $component->get('enabled'));

        $component->set('enabled', true);

        $serviceMock->shouldReceive('toggleShare')
            ->withArgs(function ($id, $enabled) use ($gallery) {
                return $id === $gallery->id && $enabled === true;
            })
            ->once();

        $component->call('toggle');
    }
}
