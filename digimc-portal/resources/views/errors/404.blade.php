@extends('layouts.app')

@section('content')
    <section class="section error-page d-flex align-items-center" style="min-height: 60vh;">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <h1 class="display-1 fw-bold text-primary">404</h1>
                    <h2 class="mb-4">{{ __('errors.page_not_found') }}</h2>
                    <p class="lead mb-5">
                        {{ __('errors.404_message') }}
                    </p>
                    <a href="{{ url('/') }}" class="btn btn-primary btn-lg">
                        <i class="fa fa-home"></i> {{ __('errors.back_to_home') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
