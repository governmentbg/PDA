@extends('layouts.app')

@section('content')
    <section class="section services">
        <div class="container my-4">
            {{--            <h2>{{ __('gallery.public_collections') }}</h2>--}}

            @if($publicGalleries->count() == 0)
                <div class="text-center">
                    {{ __('gallery.no_public_collections') }}
                </div>
            @else
                <div class="container my-4">
                    <div class="container my-3">
                        @livewire('gallery.search')
                    </div>

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
                                        {{ $gallery->objects_count ?? 0 }}
                                    </span>
                                    </div>

                                    <p class="card-text mb-0">
                                        {{ $gallery->user->name }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-4">
                {{ $publicGalleries->links() }}
            </div>
        </div>
    </section>
@endsection
