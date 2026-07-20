<?php

namespace App\Livewire\Gallery;

use App\Enums\GalleryEnum;
use App\Models\Gallery;
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
        $this->results = Gallery::query()
            ->where('status', GalleryEnum::STATUS_PUBLIC)
            ->where(function ($query) use ($searchAttr) {
                $query->where('name', $searchAttr, "%{$this->query}%")
                    ->orWhere('description', $searchAttr, "%{$this->query}%");
            })
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.gallery.search');
    }
}
