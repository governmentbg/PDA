@extends('layouts.app')

@section('content')
    <section class="section error-page d-flex align-items-center" style="min-height: 60vh;">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <h1 class="display-1 fw-bold text-danger">500</h1>
                    <h2 class="mb-4">{{ __('errors.something_went_wrong') }}</h2>
                    <p class="lead mb-5">
                        {{ __('errors.500_message') }}
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ url('/') }}" class="btn btn-primary btn-lg">
                            <i class="fa fa-home"></i> {{ __('errors.back_to_home') }}
                        </a>
                        <button onclick="window.location.reload();" class="btn btn-outline-secondary btn-lg">
                            <i class="fa fa-refresh"></i> {{ __('errors.try_again') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
