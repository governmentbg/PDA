<!DOCTYPE html>
<html>
<body>
<p>{{ __('gallery.publish_requested_email_hello', ['name' => $gallery->user->first_name . ' ' . $gallery->user->last_name]) }}</p>

<p>{{ __('gallery.publish_requested_body_intro') }}</p>

<a href="{{ route('profile.galleries.show', $gallery->id) }}">
    {{ $gallery->name }}
</a>

<p>{{ __('gallery.publish_requested_body_notice') }}</p>

<p>{{ __('gallery.email_unsubscribe_notice') }}</p>

<p>
    {{ __('gallery.email_regards') }}<br>
    {{ config('app.name') }}
</p>
</body>
</html>
