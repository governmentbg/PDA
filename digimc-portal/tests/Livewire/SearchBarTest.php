<?php

namespace Tests\Livewire;

use App\Livewire\SearchBar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SearchBarTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_search_bar_component()
    {
        Livewire::test(SearchBar::class)
            ->assertSee('');
    }

    #[Test]
    public function it_mounts_with_query_parameter()
    {
        $request = Request::create('/?q=Laravel', 'GET');

        Livewire::withQueryParams(['q' => 'Laravel'])
            ->test(SearchBar::class, ['request' => $request])
            ->assertSet('query', 'Laravel');
    }

    #[Test]
    public function it_defaults_to_empty_query_if_no_param()
    {
        Livewire::test(SearchBar::class)
            ->assertSet('query', '');
    }

    #[Test]
    public function it_redirects_to_search_route_on_search()
    {
        Livewire::test(SearchBar::class)
            ->set('query', 'Livewire')
            ->call('search')
            ->assertRedirect(route('search.index', ['q' => 'Livewire']));
    }

    #[Test]
    public function it_allows_empty_query_redirect()
    {
        Livewire::test(SearchBar::class)
            ->set('query', '')
            ->call('search')
            ->assertRedirect(route('search.index', ['q' => '']));
    }

    #[Test]
    public function it_updates_query_on_user_input()
    {
        Livewire::test(SearchBar::class)
            ->set('query', 'New Search Term')
            ->assertSet('query', 'New Search Term');
    }
}
