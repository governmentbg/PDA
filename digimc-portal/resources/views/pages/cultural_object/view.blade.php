@extends('layouts.app')

@section('content')

    <section class="section">
        @if(session('success'))
            <div class="container mb-3">
                <div class="alert alert-success d-flex align-items-center justify-content-between">
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if(!isset($culturalObject) || is_null($culturalObject))
            {{__('general.no_items_added')}}
        @else
            @php
                $selectedResourceId = request('res');
                $visualizationResource = $culturalObject->has_web_view_resource
                                                       ->when($selectedResourceId, function ($collection) use ($selectedResourceId) {
                                                           return $collection->where('id', $selectedResourceId);
                                                       })
                                                       ->first();
//                dd($visualizationResource);
                if (is_null($visualizationResource)) {
                     $visualizationResource = $culturalObject->main_web_view_resource;
                }
                $isSensitive = $visualizationResource?->isSensitive();
                $labels = $visualizationResource?->sensitiveLabels();
                $culturalObject->visualizationResource = $visualizationResource;
                $mainId = $culturalObject->main_web_view_resource?->id;
                $resources = $culturalObject->has_web_view_resource
                    ->sortByDesc(fn($item) => $item->id === $mainId)
                    ->values();

                $type = $visualizationResource?->visualizationtype;

                $map = [
                    'tiff' => 'image',
                ];

                $type = $map[$type] ?? $type;

            @endphp
            @if($visualizationResource &&
                 $visualizationResource->isPaid() &&
                 $visualizationResource->isPayableType() &&
                 !$visualizationResource->isPurchasedBy(auth()->user()))

                @php
                    $priceEur = (float) $visualizationResource->price;
                    $rate = (float) \App\Enums\SettingEnum::getValueByKeyword(\App\Enums\SettingEnum::EUR_TO_BGN);
                    $priceBgn = round($priceEur * $rate, 2);
                    $visualizationResource->is_locked = 1;
                @endphp

                <div class="container mb-3">
                    <div class="alert alert-warning d-flex align-items-center justify-content-between" role="alert">

                        <div>
                            <i class="fa fa-lock me-2"></i>
                            {{ __('cultural_object.protected_content') }}
                        </div>


                        <div class="d-flex align-items-center gap-3">
                            <div class="text-end small">
                                <div><strong>{{ number_format($priceEur, 2) }} € / {{ number_format($priceBgn, 2) }} лв.</strong></div>
                            </div>

{{--                            <form action="{{ route('profile.cart.add', ['webResource' => $visualizationResource->id]) }}" method="POST" class="m-0">--}}
{{--                                @csrf--}}
{{--                                <button type="submit" class="btn btn-sm btn-primary">--}}
{{--                                    <i class="fa fa-cart-plus me-1"></i> {{ __('cart.add_to_cart') }}--}}
{{--                                </button>--}}
{{--                            </form>--}}
                        </div>

                    </div>
                </div>
            @endif

            @if($visualizationResource && $isSensitive)
                <div id="sensitive-warning-container" class="container">
                    <div class="alert alert-light border-danger shadow-sm p-5 text-center my-4">
                        <i class="fa fa-exclamation-triangle text-danger fa-3x mb-3"></i>
                        <h3 class="h4 text-danger">{{ __('cultural_object.sensitive_content_title') }}</h3>

                        <p class="lead">
                            {{ __('cultural_object.sensitive_content_message') }}
                            @if(!empty($labels))
                                — <span class="badge bg-danger">{{ implode(', ', $labels) }}</span>
                            @endif
                            <br>
                            {{ $visualizationResource->warning_text }}
                        </p>

                        <hr class="my-4">

                        <button type="button" id="btn-reveal-content" class="btn btn-danger btn-lg px-4">
                            <i class="fa fa-eye me-2"></i>
                            {{ __('cultural_object.accept_sensitive') }}
                        </button>
                    </div>
                </div>
            @endif
            <div id="cultural-object-viewer-all" style="{{ $isSensitive ? 'display: none;' : '' }}">
                @switch($culturalObject->visualizationResource?->visualizationtype)
                    @case(\App\Enums\CulturalObjectEnum::IMAGE)
                    @case(\App\Enums\CulturalObjectEnum::TIFF)
                    <x-cultural-object-image :item="$culturalObject" :resource="$culturalObject->visualizationResource" />
                        @break
                    @case(\App\Enums\CulturalObjectEnum::PDF)
                        <x-cultural-object-pdf :item="$culturalObject" :resource="$culturalObject->visualizationResource" />
                        @break
                    @case(\App\Enums\CulturalObjectEnum::VIDEO)
                        <x-cultural-object-video :item="$culturalObject" :resource="$culturalObject->visualizationResource" />
                        @break
                    @case(\App\Enums\CulturalObjectEnum::THREE_D)
                        <x-cultural-object-3d :item="$culturalObject" :resource="$culturalObject->visualizationResource" />
                    @break
                    @case(\App\Enums\CulturalObjectEnum::AUDIO)
                        <x-cultural-object-audio :item="$culturalObject" :resource="$culturalObject->visualizationResource" />
                    @break
                    @case(\App\Enums\CulturalObjectEnum::MISC)
                        <div class="container">
                            <h3 class="card-title text-center mb-4">{{__('errors.the_object_cannot_be_visualized')}}</h3>
                        </div>
                        @break
                    @default
                        <div class="container">
                            <h3 class="card-title text-center mb-4">{{__('errors.the_object_cannot_be_visualized')}}</h3>
                        </div>
                        @break
                @endswitch
            </div>
            <div class="container">
                <div class="row">
                    <div class="d-flex justify-content-start mt-2 mb-3">
                        {{-- Thumbnails --}}
                        <div class="d-flex gap-2 flex-wrap">
                            @forelse($resources as $index => $file)
                                <x-thumbnails-display
                                    :file="$file"
                                    :culturalObject="$culturalObject"
                                    :index="$index"
                                />
                            @empty
                                <span class="text-muted"></span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

                {{-- Export button --}}
                <div class="container">
                    <div class="row">
                        <div class="d-flex justify-content-start mt-2 mb-3">
                            <div class="btn-group" role="group" aria-label="Actions">
                                <button id="export-all" type="button"
                                        class="btn btn-outline-secondary"
                                        data-batch-urls='@json(
                                        collect($export["resources"] ?? [])->map(
                                            fn($r) => route("cultural_object.export", ["id" => $culturalObject->id, "res" => $r->id]))->values()
                                        )'>
                                    <i class="fa fa-download"></i> {{ __('general.export') }}
                                </button>

                                {{-- download button --}}
                                <x-cultural-object-download :item="$culturalObject"/>

                            </div>
                        </div>
                    </div>
                </div>

            <h1 class="card-title text-center mb-4">{{ $culturalObject->title }}</h1>

            <div class="container">
                <ul class="nav nav-tabs" id="culturalObject" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="good-to-know-tab" data-bs-toggle="tab" data-bs-target="#main"
                                type="button" role="tab"
                                aria-controls="main"
                                aria-selected="true">{{__('cultural_object.good_to_know')}}</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="full-metadata-tab" data-bs-toggle="tab" data-bs-target="#full-metadata"
                                type="button" role="tab" aria-controls="full-metadata"
                                aria-selected="false">{{__('cultural_object.all_metadata')}}</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="file-metadata-tab" data-bs-toggle="tab" data-bs-target="#file-metadata"
                                type="button" role="tab" aria-controls="file-metadata"
                                aria-selected="false">{{__('cultural_object.file_metadata')}}</button>
                    </li>
                </ul>
                <div class="tab-content" id="culturalObjectContent">
                    <div class="tab-pane fade show active" id="main" role="tabpanel" aria-labelledby="main-tab">
                        <table class="table table-bordered table-striped">

                            @if(!empty($culturalObject->title))
                                <tr>
                                    <td>{{__('cultural_object.title')}}</td>
                                    <td>{{$culturalObject->title}}</td>
                                </tr>
                            @endif

                            @if(!empty($provider))
                                <tr>
                                    <td>{{__('cultural_object.provider')}}</td>
                                    <td><a href="{{route('provider.view', ['id' => $provider->id])}}">
                                            {{$provider->title}}
                                        </a></td>
                                </tr>
                            @endif



                            @if(!empty($culturalObject->type))
                                <tr>
                                    <td>{{__('cultural_object.type')}}</td>
                                    <td>{{$culturalObject->type}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->artist))
                                <tr>
                                    <td>{{__('cultural_object.artist')}}</td>
                                    <td>{{$culturalObject->artist}}</td>
                                </tr>
                            @endif


                            @if(!empty($culturalObject->description))
                                <tr>
                                    <td>{{__('cultural_object.description')}}</td>
                                    <td>{{$culturalObject->description}}</td>
                                </tr>
                            @endif


                            @if(!empty($culturalObject->creation_date))
                                <tr>
                                    <td>{{__('cultural_object.creation_date')}}</td>
                                    <td>{{$culturalObject->creation_date}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->current_location))
                                <tr>
                                    <td>{{__('cultural_object.location')}}</td>
                                    <td>{{$culturalObject->current_location}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->rights_holder))
                                <tr>
                                    <td>{{__('cultural_object.rights_holder')}}</td>
                                    <td>{{$culturalObject->rights_holder}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->keywords))
                                <tr>
                                    <td>{{__('cultural_object.keywords')}}</td>
                                    <td>{{$culturalObject->keywords}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->theme))
                                <tr>
                                    <td>{{__('cultural_object.theme')}}</td>
                                    <td>{{$culturalObject->theme}}</td>
                                </tr>
                            @endif

                        </table>
                    </div>
                    <div class="tab-pane fade" id="full-metadata" role="tabpanel" aria-labelledby="full-metadata-tab">

                        <table class="table table-bordered table-striped">
                            {{-- Cultural Object Metadata (Само полетата от Excel списъка) --}}

                            @if(!empty($culturalObject->type))
                                <tr>
                                    <td>{{__('cultural_object.type')}}</td>
                                    <td>{{$culturalObject->type}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->title))
                                <tr>
                                    <td>{{__('cultural_object.title')}}</td>
                                    <td>{{$culturalObject->title}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->original_title))
                                <tr>
                                    <td>{{__('cultural_object.original_title')}}</td>
                                    <td>{{$culturalObject->original_title}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->other_title))
                                <tr>
                                    <td>{{__('cultural_object.other_title')}}</td>
                                    <td>{{$culturalObject->other_title}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->artist))
                                <tr>
                                    <td>{{__('cultural_object.artist')}}</td>
                                    <td>{{$culturalObject->artist}}</td>
                                </tr>
                            @endif


                            @if(!empty($culturalObject->description))
                                <tr>
                                    <td>{{__('cultural_object.description')}}</td>
                                    <td>{{$culturalObject->description}}</td>
                                </tr>
                            @endif

                            @if(!empty($provider))
                                <tr>
                                    <td>{{__('cultural_object.provider')}}</td>
                                    <td><a href="{{route('provider.view', ['id' => $provider->id])}}">
                                            {{$provider->title}}
                                        </a></td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->creation_date))
                                <tr>
                                    <td>{{__('cultural_object.creation_date')}}</td>
                                    <td>{{$culturalObject->creation_date}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->current_location))
                                <tr>
                                    <td>{{__('cultural_object.location')}}</td>
                                    <td>{{$culturalObject->current_location}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->keywords))
                                <tr>
                                    <td>{{__('cultural_object.keywords')}}</td>
                                    <td>{{$culturalObject->keywords}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->theme))
                                <tr>
                                    <td>{{__('cultural_object.theme')}}</td>
                                    <td>{{$culturalObject->theme}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->subject_heading))
                                <tr>
                                    <td>{{__('cultural_object.subject_heading')}}</td>
                                    <td>{{$culturalObject->subject_heading}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->geographic_heading))
                                <tr>
                                    <td>{{__('cultural_object.geographic_heading')}}</td>
                                    <td>{{$culturalObject->geographic_heading}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->temporal_heading))
                                <tr>
                                    <td>{{__('cultural_object.temporal_heading')}}</td>
                                    <td>{{$culturalObject->temporal_heading}}</td>
                                </tr>
                            @endif



                            @if(!empty($culturalObject->language_code))
                                <tr>
                                    <td>{{__('cultural_object.language_code')}}</td>
                                    <td>{{$culturalObject->language_code}}</td>
                                </tr>
                            @endif


                            @if(!empty($culturalObject->previous_owner))
                                <tr>
                                    <td>{{__('cultural_object.previous_owner')}}</td>
                                    <td>{{$culturalObject->previous_owner}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->acquisition))
                                <tr>
                                    <td>{{__('cultural_object.acquisition')}}</td>
                                    <td>{{$culturalObject->acquisition}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->rights_holder))
                                <tr>
                                    <td>{{__('cultural_object.rights_holder')}}</td>
                                    <td>{{$culturalObject->rights_holder}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->physical_dimensions))
                                <tr>
                                    <td>{{__('cultural_object.physical_dimensions')}}</td>
                                    <td>{{$culturalObject->physical_dimensions}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->medium))
                                <tr>
                                    <td>{{__('cultural_object.medium')}}</td>
                                    <td>{{$culturalObject->medium}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->original_media))
                                <tr>
                                    <td>{{__('cultural_object.original_media')}}</td>
                                    <td>{{$culturalObject->original_media}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->contentdescription))
                                <tr>
                                    <td>{{__('cultural_object.contentdescription')}}</td>
                                    <td>{{$culturalObject->contentdescription}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->extended_view_url))
                                <tr>
                                    <td>{{__('cultural_object.extended_view')}}</td>
                                    <td>
                                        <a href="{{$culturalObject->extended_view_url}}" target="_blank">
                                            {{__('cultural_object.view_details')}}
                                        </a>
                                    </td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->amount))
                                <tr>
                                    <td>{{__('cultural_object.amount')}}</td>
                                    <td>{{$culturalObject->amount}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->currency))
                                <tr>
                                    <td>{{__('cultural_object.currency')}}</td>
                                    <td>{{$culturalObject->currency}}</td>
                                </tr>
                            @endif

                            @if($culturalObject->parent_objects->isNotEmpty())
                                @foreach($culturalObject->parent_objects as $parent)
                                    <tr>
                                        <td>{{ __('cultural_object.part_of') }}</td>
                                        <td>
                                            <a href="{{ route('cultural_object.view', ['id' => $parent->id]) }}">
                                                {{ $parent->title }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif

                            @if($culturalObject->components && $culturalObject->components->isNotEmpty())
                                <tr>
                                    <td>{{ __('cultural_object.has_components') }}</td>

                                    <td>
                                        <ul style="list-style: none; padding: 0; margin: 0;">
                                            @foreach($culturalObject->components as $component)
                                                <li>
                                                    <a href="{{ route('cultural_object.view', ['id' => $component->id]) }}">
                                                        {{ $component->title }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>

                    {{--File Metadata--}}
                    <div class="tab-pane fade" id="file-metadata" role="tabpanel" aria-labelledby="file-metadata-tab">
                        <table class="table table-bordered table-striped">

                            @if(!empty($culturalObject->visualizationResource?->title))
                                <tr>
                                    <td style="width: 30%;">{{__('cultural_object.title')}}</td>
                                    <td>{{$culturalObject->visualizationResource->title}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->visualizationResource?->description))
                                <tr>
                                    <td>{{__('cultural_object.description')}}</td>
                                    <td>{{$culturalObject->visualizationResource->description}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->visualizationResource?->creator))
                                <tr>
                                    <td>{{__('cultural_object.web_resource.creator')}}</td>
                                    <td>{{$culturalObject->visualizationResource->creator}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->visualizationResource?->rights_holder))
                                <tr>
                                    <td>{{__('cultural_object.web_resource.rights_holder')}}</td>
                                    <td>{{$culturalObject->visualizationResource->rights_holder}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->visualizationResource?->source))
                                <tr>
                                    <td>{{__('cultural_object.web_resource.source')}}</td>
                                    <td>{{$culturalObject->visualizationResource->source}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->visualizationResource?->conforms_to))
                                <tr>
                                    <td>{{__('cultural_object.web_resource.conforms_to')}}</td>
                                    <td>{{$culturalObject->visualizationResource->conforms_to}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->visualizationResource?->created_at))
                                <tr>
                                    <td>{{__('cultural_object.web_resource.created_at')}}</td>
                                    <td>{{$culturalObject->visualizationResource->created_at}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->visualizationResource?->extent))
                                <tr>
                                    <td>{{__('cultural_object.web_resource.extent')}}</td>
                                    <td>{{$culturalObject->visualizationResource->extent}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->visualizationResource?->issued))
                                <tr>
                                    <td>{{__('cultural_object.web_resource.issued')}}</td>
                                    <td>{{$culturalObject->visualizationResource->issued}}</td>
                                </tr>
                            @endif

                            @if(!empty($culturalObject->visualizationResource?->format))
                                <tr>
                                    <td>{{__('cultural_object.web_resource.format')}}</td>
                                    <td>{{$culturalObject->visualizationResource->format}}</td>
                                </tr>
                            @endif

                            @includeIf(
                               'components.web-resource-metadata-' . $type,
                               ['object' => $culturalObject]
                           )

                        </table>
                    </div>
                </div>
            </div>
            <br>
            <div class="container my-4 services">

                @if($publicGalleries->count())
                    <h2>{{ __('gallery.part_of_public_collection') }}</h2>
                    <br>
                    <div class="row gy-4">

                        @foreach($publicGalleries as $gallery)
                            <div class="col-lg-4 col-md-6">

                                <div class="service-card">

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h3 class="mb-0">
                                            <a href="{{ route('gallery.view', $gallery->id) }}"
                                               class="text-decoration-none text-reset">
                                                {{ $gallery->name }}
                                            </a>
                                        </h3>

                                        <span class="badge bg-secondary ms-2">
                                            {{ $gallery->cultural_objects_count ?? 0 }}
                                        </span>
                                    </div>

                                    <p class="card-text mb-0">
                                        {{ $gallery->user->name }}
                                    </p>

                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $publicGalleries->links() }}
                    </div>
                @endif

            </div>

        @endif
    </section>

@endsection

@push('styles')
    <style>
        .table td:nth-child(2) {
            word-break: break-word;
            overflow-wrap: anywhere;
            white-space: normal;
        }
    </style>
@endpush


@push('scripts')
    {{-- Download/export multiple files --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const buttons = document.querySelectorAll('[data-batch-urls]');
            if (!buttons) return;

            buttons.forEach(btn => {
                let urls = [];
                try {
                    urls = JSON.parse(btn.dataset.batchUrls || '[]');
                } catch (e) {
                    urls = [];
                }

                if (!Array.isArray(urls) || urls.length === 0) return;

                if (btn.dataset.bound === '1') return;
                btn.dataset.bound = '1';

                btn.addEventListener('click', (ev) => {
                    ev.preventDefault();
                    btn.disabled = true;

                    const STAGGER_MS = 400;
                    const TTL_MS = 60000;

                    urls.forEach((url, i) => {
                        setTimeout(() => {
                            const iframe = document.createElement('iframe');
                            iframe.hidden = true;
                            iframe.src = url;
                            document.body.appendChild(iframe);

                            // remove iframe
                            setTimeout(() => {
                                try {
                                    document.body.removeChild(iframe);
                                } catch (e) {}
                            }, TTL_MS);

                            // re-enable btn
                            if (i === urls.length - 1) {
                                setTimeout(() => {
                                    btn.disabled = false;
                                }, 1500);
                            }
                        }, i * STAGGER_MS);
                    });
                });
            })
        });
    </script>

    {{-- Play Overlay --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Highlight helper
            function setActiveThumb(bySrc = null, clickedEl = null) {
                const all = document.querySelectorAll('a.thumb-card[data-kind="3d"]');
                all.forEach(el => el.classList.remove('is-active','border','border-primary','shadow'));

                let target = clickedEl;
                if (!target && bySrc) {
                    for (const el of all) {
                        if (el.dataset.src === bySrc) { target = el; break; }
                    }
                }
                if (target) target.classList.add('is-active','border','border-primary','shadow');
            }

            // When a model actually finishes loading, sync highlight to its src
            document.addEventListener('three:loaded', (e) => {
                const src = e?.detail?.src;
                if (src) setActiveThumb(src, null);
            });

            // Thumbnails click handler
            document.body.addEventListener('click', (ev) => {
                const card = ev.target.closest('a.thumb-card');
                if (!card) return;

                ev.preventDefault();
                // Non-3D
                if (card.dataset.kind !== '3d') {
                    const href = card.dataset.href;
                    if (href) window.location.href = href;
                    return;
                }

                const src = card.dataset.src;
                const resId = card.dataset.res;

                let playButton = document.getElementById('three-play');

                if (!playButton) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('res', resId);

                    const currentPage = card.dataset.page;
                    if (currentPage && currentPage !== 'null' && currentPage !== '') {
                        url.searchParams.set('page', currentPage);
                    } else {
                        url.searchParams.delete('page');
                    }

                    window.location.href = url.toString();
                    return;
                }

                // Visual highlight immediately
                setActiveThumb(null, card);

                // Reflect selection in URL (no reload)
                const url = new URL(window.location.href);
                url.searchParams.set('res', resId);
                history.replaceState(null, '', url.toString());

                // Show Play overlay and queue src
                document.dispatchEvent(new CustomEvent('three:reset', { detail: { src } }));

                // Bring Viewer to focus
                const playBtn = document.getElementById('three-play');
                if (playBtn) {
                    playBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    setTimeout(() => playBtn.focus?.(), 150);
                }
            });

            // Initial active state
            try {
                const url = new URL(window.location.href);
                const initialRes = url.searchParams.get('res');
                if (initialRes) {
                    const byRes = document.querySelector(`a.thumb-card[data-kind="3d"][data-res="${initialRes}"]`);
                    if (byRes) return setActiveThumb(null, byRes);
                }
            } catch {}
            const first3d = document.querySelector('a.thumb-card[data-kind="3d"]');
            if (first3d) setActiveThumb(null, first3d);
        });
    </script>
    <script>
        $(document).ready(function() {
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                const targetTab = $(e.target).attr('data-bs-target');

                sessionStorage.setItem(
                    'activeTab_{{ $culturalObject->id }}',
                    targetTab
                );
            });

            const activeTab = sessionStorage.getItem('activeTab_{{ $culturalObject->id }}');

            if (activeTab) {
                const trigger = document.querySelector(
                    `[data-bs-target="${activeTab}"]`
                );

                if (trigger) {
                    bootstrap.Tab.getOrCreateInstance(trigger).show();
                }
            }

            $('#btn-reveal-content').on('click', function() {
                const $warning = $('#sensitive-warning-container');
                const $viewer = $('#cultural-object-viewer-all');

                $warning.fadeOut(300, function() {
                    $viewer.fadeIn(500);

                    $(document).trigger('content:revealed');
                });
            });
        });
    </script>
@endpush
