<!DOCTYPE html>
<html>
<body>
<p>{{ __('gallery.approved_title', ['name' => $gallery->user->first_name . ' ' . $gallery->user->last_name]) }}</p>

<p>
    {{ __('gallery.approved_message_before') }}
    <a href="{{ route('gallery.view', $gallery->id) }}">
        {{ $gallery->name }}
    </a>
    {{ __('gallery.approved_message_after') }}
</p>

<p>{{ __('gallery.email_unsubscribe_notice') }}</p>

<p>
    {{ __('gallery.email_regards') }}<br>
    {{ config('app.name') }}
</p>
</body>
</html>
