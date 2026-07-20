@extends('layouts.app')

@section('content')
    <section>
        @if(!isset($provider) || is_null($provider))
            {{__('general.no_items_added')}}
        @else

            <div class="container my-4">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-12">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <!-- Title -->
                                <h1 class="card-title text-center mb-4">{{ $provider->title }}</h1>
                                <h3 class="card-text mb-2">{{ $provider->description }}</h3>
                                <p class="card-text mb-2"> {!! $provider->territory !!}
                                    , {!! $provider->address !!} </p>
                                <p class="card-text mb-2"> {!! $provider->phone_number !!} </p>
                                <p class="card-text mb-2"> {!! $provider->email !!} </p>
                                <p class="card-text mb-2">
                                    <a href="{{ $provider->website }}" target="_blank" rel="noopener noreferrer">
                                        {{ $provider->website }}
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endif
    </section>

    <section class="section container">
        <h3>{{__('general.other_objects_from_this_provider')}}</h3>
        <x-cultural-objects-list :cultural-objects="$cultural_objects" :provider-name="$provider->title"
                                 :user-likes="$user_likes"/>

        {{$cultural_objects->links()}}
    </section>
@endsection
