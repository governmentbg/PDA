<?php

namespace Tests\Livewire\CulturalObject;

use App\Livewire\CulturalObject\Collections;
use App\Models\Gallery;
use App\Models\User;
use App\Models\CulturalObject;
use App\Models\GalleryCulturalObject;
use App\Services\GalleryService;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class CollectionsTest extends TestCase
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
    public function a_guest_cannot_see_collections()
    {
        $object = CulturalObject::factory()->create();
        $galleryService = $this->createMock(GalleryService::class);

        Livewire::test(Collections::class, [
            'culturalObjectIds' => [$object->id],
            'galleryService' => $galleryService,
        ])
            ->assertSet('galleries', [])
            ->assertSet('selected', []);
    }

    #[Test]
    public function a_logged_in_user_sees_their_collections_and_selected_objects()
    {
        $user = User::factory()->create();
        $object = CulturalObject::factory()->create();
        $galleryA = Gallery::factory()->for($user)->create();
        $galleryB = Gallery::factory()->for($user)->create();

        GalleryCulturalObject::create([
            'gallery_id' => $galleryA->id,
            'cultural_object_id' => $object->id,
        ]);

        $this->actingAs($user);

        $galleryService = $this->createMock(GalleryService::class);
        $galleryService->method('listForUser')->willReturn([
            'all' => collect([$galleryA, $galleryB]),
        ]);
        $galleryService->method('getObjectGalleries')->willReturn(collect([$galleryA]));

        Livewire::test(Collections::class, [
            'culturalObjectIds' => [$object->id],
            'galleryService' => $galleryService,
        ])
            ->assertSet('selected', function ($selected) use ($galleryA) {
                return isset($selected[$galleryA->id]) && $selected[$galleryA->id] === true;
            });
    }

    #[Test]
    public function a_guest_is_redirected_to_login_when_toggling_modal()
    {
        $object = CulturalObject::factory()->create();
        $galleryService = $this->createMock(GalleryService::class);

        Livewire::test(Collections::class, [
            'culturalObjectIds' => [$object->id],
            'galleryService' => $galleryService,
        ])
            ->call('toggleModal')
            ->assertRedirectToRoute('login');
    }

    #[Test]
    public function a_logged_in_user_can_toggle_the_modal()
    {
        $user = User::factory()->create();
        $object = CulturalObject::factory()->create();
        $this->actingAs($user);

        $galleryService = $this->createMock(GalleryService::class);
        $galleryService->method('listForUser')->willReturn(collect([]));
        $galleryService->method('getObjectGalleries')->willReturn(collect([]));

        Livewire::test(Collections::class, [
            'culturalObjectIds' => [$object->id],
            'galleryService' => $galleryService,
        ])
            ->assertSet('showModal', false)
            ->call('toggleModal')
            ->assertSet('showModal', true)
            ->call('toggleModal')
            ->assertSet('showModal', false);
    }

    #[Test]
    public function a_logged_in_user_can_add_an_object_to_a_collection()
    {
        $user = User::factory()->create();
        $object = CulturalObject::factory()->create();
        $gallery = Gallery::factory()->for($user)->create();

        $this->actingAs($user);

        Livewire::test(Collections::class, ['culturalObjectIds' => [$object->id]])
            ->set('selected.' . $gallery->id, true)
            ->assertDispatched('toast');

        $this->assertDatabaseHas('gallery_cultural_object', [
            'gallery_id' => $gallery->id,
            'cultural_object_id' => $object->id,
        ]);
    }

    #[Test]
    public function a_user_can_create_a_new_collection_and_add_an_object_to_it()
    {
        $user = User::factory()->create();
        $object = CulturalObject::factory()->create();
        $this->actingAs($user);

        $gallery = Gallery::factory()->make(['name' => 'My New Collection']);

        $galleryService = $this->createMock(GalleryService::class);
        $galleryService->method('create')->willReturn($gallery);
        $galleryService->method('listForUser')->willReturn(collect([]));
        $galleryService->method('getObjectGalleries')->willReturn(collect([]));
        $galleryService->method('addObjects')->willReturn(true);

        Livewire::test(Collections::class, [
            'culturalObjectIds' => [$object->id],
            'galleryService' => $galleryService,
        ])
            ->set('newGalleryName', 'My New Collection')
            ->call('createNewGallery')
            ->assertSet('newGalleryName', '')
            ->assertDispatched('toast', message: __('gallery.new_collection_created_and_added'));
    }

    #[Test]
    public function validation_fails_if_new_gallery_name_is_missing()
    {
        $user = User::factory()->create();
        $object = CulturalObject::factory()->create();
        $this->actingAs($user);

        $galleryService = $this->createMock(GalleryService::class);
        $galleryService->method('listForUser')->willReturn(collect([]));

        Livewire::test(Collections::class, [
            'culturalObjectIds' => [$object->id],
            'galleryService' => $galleryService,
        ])
            ->set('newGalleryName', '')
            ->call('createNewGallery')
            ->assertHasErrors(['newGalleryName' => 'required']);
    }
}
