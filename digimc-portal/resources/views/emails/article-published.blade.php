<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('mail.daily_news.title') }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height:1.5; color:#222;">

    <h2 style="margin:0 0 12px 0;">
        {{ __('mail.common.hello_name', ['name' => $user->first_name ?? __('mail.common.user')]) }}
    </h2>

    <p style="margin:0 0 16px 0;">
        {{ __('mail.daily_news.intro') }}
    </p>

    <!-- Titles as clickable links -->
    @foreach ($articles as $article)
        <p style="margin:0 0 8px 0;">
            <a href="{{ route('article.view', ['id' => $article->id, 'slug' => $article->slug]) }}"
               style="text-decoration:none; color:#1a73e8;">
                <strong>{{ $article->title }}</strong>
            </a>
        </p>
    @endforeach

    <p style="margin:16px 0 0 0;">
        {{ __('mail.common.regards') }}<br>
        {{ __('mail.common.team', ['app' => config('app.name')]) }}
    </p>
</body>
</html>
