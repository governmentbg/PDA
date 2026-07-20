@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-center min-h-screen bg-gray-100">
        <div class="w-full max-w-md p-8 bg-white shadow-xl rounded-2xl text-center">

            <h2 class="text-3xl font-extrabold text-gray-900 mb-4">
                {{ __('general.auth.session_expired_title') }}
            </h2>

            @if(session('status'))
                <p class="text-lg text-gray-700 mb-6">
                    {{ session('status') }}
                </p>
            @endif

            <a href="{{ route('auth.login') }}" class="btn btn-primary px-4 py-2">
                {{ __('general.auth.login_button') }}
            </a>
        </div>
    </div>
@endsection
