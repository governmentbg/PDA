<div>
    <button type="button" wire:click="toggleLike" class="p-0 border-0 bg-transparent">
        <i class="fa {{ $liked ? 'fa-heart text-danger' : 'fa-heart' }}"></i> {{ $liked ? __('cultural_object.liked') : __('cultural_object.not_liked') }}
    </button>
</div>
