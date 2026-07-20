<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Http\Request;

class SearchBar extends Component
{
    public string $query = '';

    public function mount(Request $request)
    {
        $this->query = $request->query('q', '');
    }

    public function search()
    {
        return redirect()->route('search.index', ['q' => $this->query]);
    }

    public function openAdvancedSearch()
    {
        $q = trim($this->query ?? '');

        return redirect()->route('search.index', array_filter([
            'q' => $q !== '' ? $q : null,
            'advanced' => 1,
        ]));
    }

    public function render()
    {
        return view('livewire.search-bar');
    }
}
