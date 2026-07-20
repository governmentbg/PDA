<?php

namespace Tests\Livewire\Gallery;

use App\Enums\GalleryEnum;
use App\Livewire\Gallery\Search;
use App\Models\Gallery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_no_results_with_less_than_three_characters()
    {
        Livewire::test(Search::class)
            ->set('query', 'ab')
            ->call('search')
            ->assertSet('results', []);
    }

    #[Test]
    public function it_returns_matching_public_galleries_after_three_characters()
    {
        Gallery::factory()->create([
            'name' => 'Alpha Gallery',
            'status' => GalleryEnum::STATUS_PUBLIC,
        ]);

        Gallery::factory()->create([
            'name' => 'Beta Collection',
            'status' => GalleryEnum::STATUS_PUBLIC,
        ]);

        Livewire::test(Search::class)
            ->set('query', 'Alp')
            ->call('search')
            ->assertSee('Alpha Gallery')
            ->assertDontSee('Beta Collection');
    }

    #[Test]
    public function it_is_case_insensitive()
    {
        Gallery::factory()->create([
            'name' => 'Delta Gallery',
            'status' => GalleryEnum::STATUS_PUBLIC,
        ]);

        Livewire::test(Search::class)
            ->set('query', 'delta')
            ->call('search')
            ->assertSee('Delta Gallery');
    }

    #[Test]
    public function it_does_not_return_private_galleries()
    {
        Gallery::factory()->create([
            'name' => 'Private Gallery',
            'status' => GalleryEnum::STATUS_PRIVATE,
        ]);

        Gallery::factory()->create([
            'name' => 'Private Museum',
            'status' => GalleryEnum::STATUS_PUBLIC,
        ]);

        Livewire::test(Search::class)
            ->set('query', 'Private')
            ->call('search')
            ->assertSee('Private Museum')
            ->assertDontSee('Private Gallery');
    }

    #[Test]
    public function it_returns_galleries_matching_description()
    {
        Gallery::factory()->create([
            'name' => 'Art Gallery',
            'description' => 'Random text for testing',
            'status' => GalleryEnum::STATUS_PUBLIC,
        ]);

        Livewire::test(Search::class)
            ->set('query', 'random')
            ->call('search')
            ->assertSee('Art Gallery');
    }
}
