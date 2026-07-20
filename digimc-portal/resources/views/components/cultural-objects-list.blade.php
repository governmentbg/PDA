@if($culturalObjects->count() == 0)
    <div class="text-center">
        {{ __('general.no_objects_found') }}
    </div>
@else
    <section class="section cultural">
        <div class="container my-4">
            <div class="row gy-4">
                @foreach($culturalObjects->items() as $k => $item)

                    <div class="col-lg-6 col-sm-12">

                            <div class="cultural-object d-flex">
                                <a href="{{ route('cultural_object.view', ['id' => $item['id']]) }}"
                                   class="text-decoration-none d-block">
                                <div class="object-img h-100">
                                    @if(!empty($item['thumbnail_url']))
                                        <img
                                            src="{{$item['thumbnail_url']}}"
                                            alt="{{$item['title']}}" class="img-fluid h-100 object-fit-cover">

                                    @else

                                        <x-thumbnail-placeholder
                                            :type="$item['main_web_view_resource']['visualizationtype'] ?? 'default'"
                                            :title="$item['title']"
                                            class="h-100" />
                                    @endif
                                </div>
                                </a>
                                <div class="object-info flex-grow-1">
                                    <a href="{{ route('cultural_object.view', ['id' => $item['id']]) }}"
                                       class="text-decoration-none d-block">
                                    <h4>{{$item['title']}}</h4>
                                    <p>{{ \Illuminate\Support\Str::limit($item['description']) }}</p>

                                    @if(!empty($item['rights']))
                                    <p>{{$item['rights']}}</p>
                                    @endif

                                    @if(!empty($item['provider']['title']) || !empty($providerName))
                                        <div class="align-text-bottom">
                                            @if(!empty($item['provider']['title']))
                                                <p class="text-muted small">
                                                    {{$item['provider']['title']}}
                                                </p>
                                            @endif

{{--                                            @if(!empty($providerName))--}}
{{--                                                <p class="text-muted small">--}}
{{--                                                    {{$providerName}}--}}
{{--                                                </p>--}}
{{--                                            @endif--}}
                                        </div>
                                    @endif
                                    </a>
                                    <div class="object-buttons custom-button-group mt-auto">

                                        <div class="button-wrapper position-relative">
                                            <input type="checkbox" class="form-check-input select-object"
                                                   data-id="{{ $item['id'] }}"
                                                   id="co_{{ $item['id'] }}">

                                            <label for="co_{{ $item['id'] }}" class="full-click-label">
                                                {{ __('general.select') }}
                                            </label>
                                        </div>

                                        <div class="button-wrapper">
                                            @livewire('cultural_object.like', [
                                                'cultural_object_id' => $item['id'],
                                                $userLikes->where('cultural_object_id', $item['id'])->count() > 0
                                            ], key('like-'.$item['id']))
                                        </div>

                                        <div class="button-wrapper">
                                            @livewire('cultural-object.collections', [
                                                'culturalObjectIds' => [$item['id']]
                                            ], key('collections-'.$item['id']))
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

@push('styles')
    <style>

        .cultural-object .object-info {
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            padding-bottom: 5px;
        }

        .cultural-object .object-info h4,
        .cultural-object .object-info p,
        .cultural-object .object-info div {
            flex-shrink: 0;
            flex-grow: 0;
        }


        .cultural-object .object-info .object-buttons {
            margin-top: auto; /
            flex-shrink: 0;
            padding-top: 10px;
        }

        .cultural-object {
            min-height: 230px;
        }

        .cultural-object .object-info {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .cultural-object .object-info p {
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }

        .cultural-object .object-buttons {
            margin-top: auto;
        }

        .object-img {
            width: 200px;
            height: 200px;
            flex-shrink: 0;
            overflow: hidden;
            border-radius: 6px;
        }

        .object-img img,
        .object-img svg,
        .object-img .thumbnail-placeholder {
            width: 100%;
            height: 100%;
            object-fit: cover !important;
            display: block;
        }

        .custom-button-group {
            display: flex;
            gap: 5px;
        }

        .custom-button-group .button-wrapper {
            border: 1px solid #dee2e6;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 100px;
            padding: 5px 10px;
            gap: 5px;

        }


        .custom-button-group .button-wrapper input[type="checkbox"] {
            margin: 0;
            cursor: pointer;
        }

        .custom-button-group .button-wrapper label {
            margin: 0;
            line-height: 1;
            cursor: pointer;
        }


    </style>
@endpush
