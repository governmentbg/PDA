@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">{{ __('general.auth.resend_activation_title') }}</div>
                    <div class="card-body">
                        <p>
                            {{ __('general.auth.resend_activation_description') }}
                        </p>

                        <form method="POST" action="{{ route('auth.resend-activation.post') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('general.auth.email') }}</label>
                                <input type="email" name="email" id="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" required autofocus>
                                @error('email')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">
                                {{ __('general.auth.send_activation_email_button') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
