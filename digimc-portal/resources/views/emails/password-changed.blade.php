<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>
        {{ __('profile.password_changed.title') }}
    </title>
</head>

<body style="font-family: Arial, sans-serif; line-height:1.5; color:#222;">

<h2>{{ __('profile.common.hello_name', ['name' => $user->first_name]) }}!</h2>

<p>
    {{ __('profile.password_changed.intro') }}
</p>

<p>
    <a href="{{ route('profile.show') }}"
       style="background:#4CAF50;color:#fff;padding:10px 20px;text-decoration:none;border-radius:5px;display:inline-block;">
        {{ __('profile.common.view_profile') }}
    </a>
</p>

<p>{{ __('profile.password_changed.if_not_you') }}</p>

<p>{{ __('profile.common.regards') }}<br>{{ __('profile.common.team', ['app' => config('app.name')]) }}</p>
</body>
</html>
