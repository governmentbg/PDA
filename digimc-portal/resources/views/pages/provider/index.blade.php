@extends('layouts.app')

@section('content')
    <section class="section services">
        @if(!isset($providers) || $providers->count() == 0)
            {{__('general.no_items_added')}}
        @else

            <div class="container my-4">
                <div class="container my-3">
                    @livewire('provider.search')
                </div>

                <div class="row gy-4">

                    @foreach($providers as $key => $provider)
                        <!-- Provider -->
                        <div class="col-lg-4 col-md-6">
                            <div class="service-card">
                                <h3>{{$provider->title}}</h3>
                                <p class="card-text"> {!! $provider->territory !!}
                                    , {!! $provider->address !!} </p>

                                <a href="{{route('provider.view', ['id' => $provider->id])}}" class="service-link">
                                    {{ __('article.read_more') }}
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        <!-- End Provider Card -->


                    @endforeach
                </div>
            </div>



            {{ $providers->links() }}
        @endif
    </section>

@endsection
