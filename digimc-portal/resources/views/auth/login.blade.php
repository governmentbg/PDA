@extends('layouts.app')

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><h2>{{ __('general.auth.login_title') }}</h2></div>
                    <div class="card-body">

                        @if($errors->any())
                            <div class="alert alert-danger">
                                @foreach($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        @if(session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('auth.login') }}">
                            @csrf

                            <div class="mb-3">
                                <label>{{ __('general.auth.email') }}</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                            </div>

                            <div class="mb-3">
                                <label>{{ __('general.auth.password') }}</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>



                            @if($recaptchaEnabled && session('login_attempts', 0) >= 3)
                                <div class="mb-3">
                                    <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
                                    @error('g-recaptcha-response')
                                    <span class="text-danger" role="alert">
                                            <strong>{{ __('general.auth.robot') }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            @endif

                            <button type="submit" class="btn btn-primary">{{ __('general.auth.login_button') }}</button>
                        </form>

                            <div class="d-flex justify-content-between mt-3">
                                <a href="{{ route('auth.register') }}">{{ __('general.auth.no_account') }}</a>
                                <a href="{{ route('auth.password.request') }}">{{ __('general.auth.forgot_password') }}</a>
                            </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- 🛑 Load the reCAPTCHA v2 API script. No 'render=' parameter needed for v2 checkbox --}}
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    {{-- 🛑 Remove the custom JavaScript for executing v3 --}}
@endpush
