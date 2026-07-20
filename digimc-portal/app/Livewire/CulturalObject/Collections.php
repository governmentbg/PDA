<?php

namespace App\Livewire\CulturalObject;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\GalleryService;
use Livewire\Attributes\On;

class Collections extends Component
{
    /** @var int[] */
    public array $culturalObjectIds = [];

    /** @var array */
    public array $galleries = [];

    /**
     * @var array
     */
    public array $selected = [];

    public string $newGalleryName = '';
    public string $newGalleryDescription = '';
    public bool $showModal = false;

    protected array $rules = [
        'newGalleryName' => 'required|string|min:3|max:255',
        'newGalleryDescription' => 'nullable|string',
    ];

    public function mount(int|array $culturalObjectIds)
    {
        $ids = is_array($culturalObjectIds)
            ? $culturalObjectIds
            : [$culturalObjectIds];

        $this->culturalObjectIds = array_map('intval', $ids);

        if (Auth::check()) {
            $this->refreshGalleries();
            $this->handleIdUpdate($this->culturalObjectIds);
        }
    }

    #[On('global-gallery-created')]
    public function handleGlobalGalleryCreated()
    {
        $this->refreshGalleries();
    }

    public function updatedCulturalObjectIds($value)
    {
        $ids = is_array($value) ? $value : [$value];
        $ids = array_map('intval', $ids);
        $this->handleIdUpdate($ids);
    }

    protected function handleIdUpdate(array $ids)
    {
        $this->culturalObjectIds = $ids;

        if (Auth::check()) {
            if (count($this->culturalObjectIds) === 1) {
                $this->refreshCollectionStatus();
            } else {
                $this->selected = [];
            }
        }
    }

    #[On('collection-membership-updated')]
    public function refreshCollectionStatus()
    {
        $galleryService = app(GalleryService::class);

        if (Auth::check() && count($this->culturalObjectIds) === 1) {
            $objectId = $this->culturalObjectIds[0];
            $attached = $galleryService->getObjectGalleries($objectId);
            $attachedIds = collect($attached)->pluck('id')->toArray();

            $this->selected = [];
            foreach ($this->galleries as $gallery) {
                $this->selected[$gallery['id']] = in_array($gallery['id'], $attachedIds);
            }
        }
    }

    #[On('gallery-created')]
    public function refreshGalleries()
    {
        $galleryService = app(GalleryService::class);
        $lists = $galleryService->listForUser();
        $this->galleries = $lists['all']->toArray();

        if (count($this->culturalObjectIds) === 1) {
            $this->refreshCollectionStatus();
        }
    }

    public function toggleModal()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (count($this->culturalObjectIds) === 1) {
            $this->refreshCollectionStatus();
        }

        $this->showModal = !$this->showModal;
    }

    public function updatedSelected($value, $key)
    {
        if (!Auth::check()) {
            return;
        }

        $galleryService = app(GalleryService::class);
        $galleryId = (int) $key;
        $isChecked = (bool) $value;

        try {
            if ($isChecked) {
                $success = $galleryService->addObjects($galleryId, $this->culturalObjectIds);
            } else {
                $success = $galleryService->removeObjects($galleryId, $this->culturalObjectIds);
            }

            if ($success) {
                $this->dispatch('toast', message: __('gallery.objects_updated_in_collection'));
            } else {
                $this->dispatch('toast', message: __('gallery.collection_update_error'));
            }

        } catch (\Exception $e) {
//            session()->flash('debug_error', 'Service Error: ' . $e->getMessage());
            $this->dispatch('toast', message: __('gallery.collection_update_error'));
        }
    }

    public function createNewGallery()
    {
        $this->validateOnly('newGalleryName');
        $this->validateOnly('newGalleryDescription');
        $galleryService = app(GalleryService::class);

        try {
            $request = new Request([
                'name' => $this->newGalleryName,
                'description' => $this->newGalleryDescription,
            ]);
            $gallery = $galleryService->create($request);

            $galleryService->addObjects($gallery->id, $this->culturalObjectIds);

            $this->newGalleryName = '';
            $this->newGalleryDescription = '';
            $this->refreshGalleries();
            $this->dispatch('global-gallery-created', broadcast: true);
            $this->dispatch('toast', message: __('gallery.new_collection_created_and_added'));
            $this->dispatch('collection-membership-updated');

        } catch (\Exception $e) {
            $this->dispatch('toast', message: __('gallery.collection_creation_error'));
        }
    }

    public function render()
    {
        if (session()->has('debug_error')) {
//             dd(session('debug_error'));
        }

        return view('livewire.cultural_object.collections');
    }
}
