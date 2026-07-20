
@once
    @push('css')
        <style>
            .thumb-card{
                position:relative; display:inline-flex; align-items:center; justify-content:center;
                width:150px; height:100px;
                border-radius:.5rem; overflow:hidden; text-decoration:none; background:#b9c1c9; cursor:pointer;
            }
            .thumb-card img{ display:block; width:100%; height:100%; object-fit:cover; position:relative; z-index:1; }
            .thumb-card::after{ content:""; position:absolute; inset:0; background:rgba(0,0,0,.18); opacity:0; transition:opacity .2s; pointer-events:none; z-index:1; }
            .thumb-card:hover::after, .thumb-card:focus-visible::after{ opacity:1; }
            .thumb-card.is-active { border:2px solid #0d6efd !important; box-shadow:0 0 0 .1rem rgba(13,110,253,.25); }
        </style>
    @endpush
@endonce

@php
    $isPurchased = $file->isPurchasedBy(auth()->user());
    $isLocked = $file->isPaid() && $file->isPayableType() && !$isPurchased;

    $thumbStatus = null;
    if ($isLocked) $thumbStatus = 'locked';
    elseif ($isPurchased) $thumbStatus = 'purchased';
@endphp

<a href="#"
   class="thumb-card d-inline-flex align-items-center justify-content-center rounded overflow-hidden"
   @if($file->visualizationtype === \App\Enums\CulturalObjectEnum::THREE_D)
       data-src="{{ $thumbStatus === 'locked' && $file->trailer_address ? $file->trailer_address : $file->web_resource_address }}"
   data-res="{{ $file->id }}"
   data-kind="3d"
   @else
       data-href="{{ route('cultural_object.view', array_filter(['id' => $culturalObject->id, 'res' => $file->id, 'page' => request()->query('page')])) }}"
   data-res="{{ $file->id }}"
   data-kind="other"
   @endif
   style="width:150px; height:100px; text-decoration:none; position:relative;"
   aria-label="Preview thumbnail"
   title="{{ $file->visualizationtype ?? 'resource' }}"
>
        <x-thumbnail-placeholder
            :type="$file['visualizationtype'] ?? 'default'"
            :title="$culturalObject['title']"
            :status="$thumbStatus"
            class="h-100 object-fit:cover" />
</a>

