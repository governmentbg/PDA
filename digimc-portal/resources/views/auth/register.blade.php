@extends('layouts.app')

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2>{{ __('general.auth.register_title') }}</h2>
                    </div>
                    <div class="card-body">
                        @livewire('register-form')

                        <div class="mt-3">
                            <a href="{{ route('auth.resend-activation') }}" class="btn btn-secondary">
                                {{ __('general.auth.resend_activation_email') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
