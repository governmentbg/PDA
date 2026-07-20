<?php

namespace App\Livewire\Gallery;

use Alert;
use Livewire\Component;
use App\Services\GalleryService;
use App\Enums\GalleryEnum;

class ShareSwitch extends Component
{
    public int $galleryId;
    public bool $enabled;

    public function mount(int $galleryId, string $status)
    {
        $this->galleryId = $galleryId;
        $this->enabled = $status === GalleryEnum::STATUS_PUBLIC;
    }

    public function toggle(GalleryService $service, bool $enabled)
    {
        $this->enabled = $enabled;
        $result = $service->toggleShare($this->galleryId, $this->enabled);
        if ($result === GalleryEnum::STATUS_PENDING) {
            Alert::success(__('gallery.publish_request_title'), __('gallery.publish_request_message'));
            $this->redirectRoute('profile.galleries.index', ['tab' => 'pending']);
            return;
        }
        $this->dispatch('refresh-switch-button');
    }

    public function render()
    {
        return view('livewire.gallery.share-switch');
    }
}
