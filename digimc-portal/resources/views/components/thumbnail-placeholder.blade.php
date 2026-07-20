@props(['type' => 'default', 'title' => '', 'class' => '', 'status' => null])

@php
    $types = [
        'image' => ['icon' => 'fa-regular fa-image', 'color' => '#6c63ff'],
        'tiff' => ['icon' => 'fa-regular fa-image', 'color' => '#5bc0de'],
        'video' => ['icon' => 'fa-solid fa-video', 'color' => '#d63384'],
        '3d' => ['icon' => 'fa-solid fa-cube', 'color' => '#20c997'],
        'pdf' => ['icon' => 'fa-regular fa-file-pdf', 'color' => '#dc3545'],
        'audio' => ['icon' => 'fa-solid fa-music', 'color' => '#0d6efd'],
        'default' => ['icon' => 'fa-regular fa-file', 'color' => '#adb5bd'],
    ];

    $bgInfo = $types[strtolower($type)] ?? $types['default'];

    $overlay = match ($status) {
        'locked' => [
            'icon' => 'fa-solid fa-lock',
            'text' => null,
        ],
        'purchased' => [
            'icon' => 'fa-solid fa-circle-check',
            'text' => null,
        ],
        default => null
    };
@endphp
<div {{ $attributes->merge([
    'class' => "d-flex align-items-center justify-content-center rounded text-white fw-bold overflow-hidden $class"
]) }}
     style="background-color: {{ $bgInfo['color'] }};
            width:100%; height:100%; position:relative;">

    <i class="{{ $bgInfo['icon'] }} fs-1" title="{{ $title }}"></i>

    @if($overlay)
        <div style="position:absolute; bottom:0; left:0; right:0;
                    background:rgba(0,0,0,.6); color:white; font-size:.75rem;
                    text-align:center; padding:2px;">
            <i class="{{ $overlay['icon'] }}"></i>
        </div>
    @endif
</div>
