<div class="container">
<div class="row justify-content-center">
    <p class="registration-instruction mb-4">
        {{ __('general.auth.register_instruction') }}
    </p>
    <div class="col-md-6">

        <form wire:submit.prevent="register">

            <div class="mb-3">
                <label for="first_name">{{ __('general.auth.first_name') }}</label>
                <input type="text" id="first_name" wire:model.live="first_name" class="form-control">
                <span class="required-note">{{ __('general.auth.required') }}</span>
                @if($errors->has('first_name'))
                    <span class="text-danger">{{ $errors->first('first_name') }}</span>
                @endif
            </div>

            <div class="mb-3">
                <label for="last_name">{{ __('general.auth.last_name') }}</label>
                <input type="text" id="last_name" wire:model.live="last_name" class="form-control">
                <span class="required-note">{{ __('general.auth.required') }}</span>
                @if($errors->has('last_name'))
                    <span class="text-danger">{{ $errors->first('last_name') }}</span>
                @endif
            </div>

            <div class="mb-3">
                <label for="email">{{ __('general.auth.email') }}</label>
                <input type="email" id="email" wire:model.blur="email" class="form-control">
                <span class="required-note">{{ __('general.auth.email_required_note') }}</span>
                @if($errors->has('email'))
                    <span class="text-danger">{{ $errors->first('email') }}</span>
                @endif
            </div>


            <div class="mb-3">
                <label for="password">{{ __('general.auth.create_password') }}</label>
                <input type="password" id="password" wire:model.live="password" class="form-control">
                <span class="required-note">{{ __('general.auth.password_min_length') }}</span>
                @if($password)
                    <div class="password-strength mt-1">
                        {{ __('general.auth.password_strength') }}:
                        <span class="strength-{{ strtolower($this->passwordStrength()) }}">
                                {{ $this->passwordStrength() }}
                            </span>
                    </div>
                @endif
                @if($errors->has('password'))
                    <span class="text-danger">{{ $errors->first('password') }}</span>
                @endif
            </div>

            <div class="mb-3">
                <label for="password_confirmation">{{ __('general.auth.confirm_password') }}</label>
                <input type="password" id="password_confirmation" wire:model.live="password_confirmation" class="form-control">

                @if($password && $password_confirmation && $password !== $password_confirmation)
                    <span class="text-danger">{{__('validation.password.match')}}</span>
                @endif

                @if($errors->has('password_confirmation'))
                    <span class="text-danger">{{ $errors->first('password_confirmation') }}</span>
                @endif
            </div>

            <div class="form-check mb-2">
                <input type="checkbox" id="wants_notifications" wire:model="wants_notifications" class="form-check-input">
                <label class="form-check-label" for="wants_notifications">{{ __('general.auth.receive_notifications') }}</label>
            </div>

            <div class="form-check mb-2">
                <input type="checkbox" id="subscribed_news" wire:model="subscribed_news" class="form-check-input">
                <label class="form-check-label" for="subscribed_news">{{ __('general.auth.news_subscription') }}</label>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" id="subscribed_weekly" wire:model="subscribed_weekly" class="form-check-input">
                <label class="form-check-label" for="subscribed_weekly">{{ __('general.auth.weekly_newsletter') }}</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">{{ __('general.auth.register_button') }}</button>
        </form>

        <div class="mt-3 text-center">
            <a href="{{ route('auth.login') }}">{{ __('general.auth.login_title') }}</a>
        </div>

    </div>
</div>
</div>



@push('styles')
    <style>
        .registration-instruction {
            font-size: 0.9em;
            color: #555;
            line-height: 1.5;
        }
        .password-strength { font-size: 0.8em; margin-top: 5px; color: #666; }
        .strength-{{__('validation.password.weak')}} { color: red; }
        .strength-{{__('validation.password.normal')}} { color: orange; }
        .strength-{{__('validation.password.strong')}} { color: green; }
        .registration-container {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            min-height: 100vh;
        }

        .logo img {
            height: 40px;
        }
        .navigation-links a {
            text-decoration: none;
            color: #007bff;
            font-size: 1.1em;
            padding: 5px 10px;
            border-bottom: 2px solid transparent;
        }
        .navigation-links a.active {
            color: #000;
            font-weight: bold;
            border-bottom-color: #000;
        }

        .content-section h2 {
            font-size: 1.8em;
            color: #333;
        }
        .content-section p {
            font-size: 0.9em;
            color: #666;
            line-height: 1.5;
        }

        .form-group label {
            display: block;
            font-size: 0.9em;
            color: #555;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .required-note {
            display: block;
            font-size: 0.8em;
            color: #888;
            margin-top: 5px;
        }

        .password-strength {
            font-size: 0.8em;
            margin-top: 5px;
            color: #666;
        }

        .checkbox-group input {
            margin-right: 10px;
            margin-top: 3px;
        }
        .checkbox-group label a {
            color: #007bff;
            text-decoration: none;
        }

    </style>
@endpush
