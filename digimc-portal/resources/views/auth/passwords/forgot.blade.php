@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><h2>{{ __('general.auth.forgot_password_title') }}</h2></div>

                    <div class="card-body">

                        @if(session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                @foreach($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('auth.password.email') }}">
                            @csrf

                            <div class="mb-3">
                                <label>{{ __('general.auth.email') }}</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                            </div>

                            <button type="submit" class="btn btn-primary">{{ __('general.auth.send_link_button') }}</button>
                        </form>

                        <div class="mt-3">
                            <a href="{{ route('auth.login') }}">{{ __('general.auth.back_to_login') }}</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
