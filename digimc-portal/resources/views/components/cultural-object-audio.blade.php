@props(['item', 'resource'])

<div class="container">
    <audio
        id="audio-{{ $item->id }}"
        class="w-100"
        controls
        controlslist="nodownload"
        preload="metadata"
        src="{{ $resource?->is_locked ? $resource?->trailer_address : $resource?->web_resource_address }}">
    </audio>

</div>
