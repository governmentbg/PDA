<?php

namespace Tests\Livewire\Provider;

use App\Livewire\Provider\Search;
use App\Models\Provider;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
    public function it_returns_matching_providers_after_three_characters()
    {
        Provider::factory()->create(['title' => 'Alpha Provider']);
        Provider::factory()->create(['title' => 'Beta Services']);

        Livewire::test(Search::class)
            ->set('query', 'Alp')
            ->call('search')
            ->assertSee('Alpha Provider')
            ->assertDontSee('Beta Services');
    }

    #[Test]
    public function it_is_case_insensitive()
    {
        Provider::factory()->create(['title' => 'Delta Solutions']);

        Livewire::test(Search::class)
            ->set('query', 'delta')
            ->call('search')
            ->assertSee('Delta Solutions');
    }
}
