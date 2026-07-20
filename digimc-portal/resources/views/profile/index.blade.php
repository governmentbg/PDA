@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">
                {{ __('profile.title_show') }}
            </h1>

            <div class="d-flex gap-2">
                <a class="btn btn-primary" href="{{ route('profile.edit') }}">
                    {{ __('profile.buttons.edit') }}
                </a>
            </div>
        </div>

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="row g-4 align-items-center">
                    <div class="col-auto">
                        @php
                            $photo = $user->profile_image_path ? asset($user->profile_image_path) : null;
                        @endphp

                        @if($photo)
                            <img src="{{ $photo }}" alt="Profile photo"
                                 class="rounded-circle border"
                                 style="width: 96px; height: 96px; object-fit: cover;">
                        @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center border bg-light"
                                 style="width: 96px; height: 96px;">
                                <span class="text-muted">{{ __('profile.fields.no_photo') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="col">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="text-muted small">{{ __('profile.fields.first_name') }}</div>
                                <div class="fw-semibold">{{ $user->first_name ?? '—' }}</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="text-muted small">{{ __('profile.fields.last_name') }}</div>
                                <div class="fw-semibold">{{ $user->last_name ?? '—' }}</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="text-muted small">{{ __('profile.fields.email') }}</div>
                                <div class="fw-semibold">{{ $user->email }}</div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="text-muted small">{{ __('profile.fields.wants_notifications') }}</div>
                                    <div class="fw-semibold">
                                        {{ $user->wants_notifications ? __('profile.yes') : __('profile.no') }}
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <div class="text-muted small">{{ __('profile.fields.subscribed_news') }}</div>
                                    <div class="fw-semibold">
                                        {{ $user->subscribed_news ? __('profile.yes') : __('profile.no') }}
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <div class="text-muted small">{{ __('profile.fields.subscribed_weekly') }}</div>
                                    <div class="fw-semibold">
                                        {{ $user->subscribed_weekly ? __('profile.yes') : __('profile.no') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
