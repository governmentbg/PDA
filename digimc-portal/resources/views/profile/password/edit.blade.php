@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="h3 mb-3">
            {{ __('profile.title_pass') }}
        </h1>

        @if ($errors->has('general'))
            <div class="alert alert-danger">{{ $errors->first('general') }}</div>
        @endif

        {!! html()->form('POST', route('profile.password.update'))
             ->attributes(['novalidate' => true, 'id' => 'passwordForm'])
             ->open() !!}
            @csrf

            {{-- Current password --}}
            <div class="mb-3">
                {!! html()->label(__('profile.fields.current_password'), 'current_password')->class('form-label') !!}
                {!! html()->password('current_password')
                      ->class('form-control'.($errors->has('current_password') ? ' is-invalid' : ''))
                      ->attribute('autocomplete','current-password') !!}
                @if($errors->has('current_password'))
                    <div class="invalid-feedback">{{ $errors->first('current_password') }}</div>
                @endif
            </div>

            {{-- New password --}}
            <div class="mb-3">
                {!! html()->label(__('profile.fields.new_password'), 'password')->class('form-label') !!}
                {!! html()->password('password')
                      ->class('form-control'.($errors->has('password') ? ' is-invalid' : ''))
                      ->attribute('autocomplete','new-password')->required() !!}
                @if($errors->has('password'))
                    <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                @endif
            </div>

            {{-- Confirm new password --}}
            <div class="mb-3">
                {!! html()->label(__('profile.fields.password_confirmation'), 'password_confirmation')->class('form-label') !!}
                {!! html()->password('password_confirmation')
                      ->class('form-control'.($errors->has('password_confirmation') ? ' is-invalid' : ''))
                      ->attribute('autocomplete','new-password')->required() !!}
                @if($errors->has('password_confirmation'))
                    <div class="invalid-feedback">{{ $errors->first('password_confirmation') }}</div>
                @endif
            </div>

        <div class="d-flex gap-2">
            <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">{{ __('profile.buttons.cancel') }}</a>
            {!! html()->button(__('profile.buttons.save'))->type('submit')->class('btn btn-primary ms-auto') !!}
        </div>

        {!! html()->form()->close() !!}
    </div>
@endsection
