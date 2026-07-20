<!DOCTYPE html>
<html>
<body>
@if ($actionType === 'reject')

    <p>{{ __('gallery.rejected_title', ['name' => $gallery->user->first_name . ' ' . $gallery->user->last_name]) }}</p>

    <p>
        {{ __('gallery.rejected_message_before') }}
        <a href="{{ route('profile.galleries.show', $gallery->id) }}">
            {{ $gallery->name }}
        </a>

        @if(!empty(trim($reason)))
            {{ __('gallery.rejected_message_reason') }}
            {{ $reason }}.
        @else
            {{ __('gallery.rejected_message_after') }}
        @endif
    </p>

@elseif ($actionType === 'unpublish')

    <p>{{ __('gallery.unpublished_title', ['name' => $gallery->user->first_name . ' ' . $gallery->user->last_name]) }}</p>
    <p>
        {{ __('gallery.unpublished_message_before') }}
        <a href="{{ route('profile.galleries.show', $gallery->id) }}">
            {{ $gallery->name }}
        </a>
        {{ __('gallery.unpublished_message_after') }}
    </p>

@endif

<p>{{ __('gallery.email_unsubscribe_notice') }}</p>

<p>
    {{ __('gallery.email_regards') }}<br>
    {{ config('app.name') }}
</p>
</body>
</html>
