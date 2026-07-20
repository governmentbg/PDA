        @php
            $downloadableResources = collect($item->has_web_view_resource ?? [])
                  ->reject(fn($r) =>
                      in_array($r->visualizationtype, [
                          \App\Enums\CulturalObjectEnum::VIDEO,
                          \App\Enums\CulturalObjectEnum::AUDIO,
                          \App\Enums\CulturalObjectEnum::TIFF,
                      ])
                      || $r->isPaid()
                  );
        @endphp

        @if ($downloadableResources->count() === 1)
            <a href="{{ route('cultural_object.download', ['id' => $item->id, 'res' => $downloadableResources->first()->id]) }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-download me-1"></i> {{ __('general.download') }}
            </a>
        @elseif ($downloadableResources->count() > 1)
            <button id="download-all" class="btn btn-outline-secondary"
                    data-batch-urls='@json(
                        $downloadableResources->map(fn($r) => route('cultural_object.download', ['id' => $item->id, 'res' => $r->id]))->values()
                    )'>
                <i class="bi bi-download me-1"></i> {{ __('general.download') }}
            </button>
        @endif
