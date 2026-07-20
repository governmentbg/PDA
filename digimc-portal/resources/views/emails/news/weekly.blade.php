<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('mail.news.title') }}</title></head>
<body style="font-family: Arial, sans-serif; line-height:1.5; color:#222;">
<h2>{{ __('mail.common.hello_name', ['name' => $user->first_name ?? __('mail.common.user')]) }}</h2>

<p>
    {{ __('mail.news.intro', [
        'from' => $from->toFormattedDateString(),
        'to' => $to->toFormattedDateString()
    ]) }}
</p>

<ul>
    @foreach($news as $item)
        <li>
            <strong>{{ $item->title }}</strong>
            <div style="font-size:12px; color:#666;">
                {{ optional($item->published_at)->toDayDateTimeString() }}
            </div>

            <div>{{ $item->content }}</div>
        </li>
    @endforeach
</ul>

<p>{{ __('mail.common.regards') }}<br>{{ __('mail.common.team', ['app' => config('app.name')]) }}</p>
</body>
</html>
