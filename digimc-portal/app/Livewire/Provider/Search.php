<?php

namespace App\Livewire\Provider;

use App\Models\Provider;
use Livewire\Component;

class Search extends Component
{
    public string $query = '';
    public $results = [];

    public function search()
    {
        if (strlen($this->query) < 3) {
            $this->results = [];
            return;
        }

        $searchAttr = config('app.env') == 'testing' ? 'LIKE' : 'ILIKE';
        $this->results = Provider::query()
            ->where('title', $searchAttr, "%{$this->query}%")
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.provider.search');
    }
}
