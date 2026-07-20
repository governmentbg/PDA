<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Промяна на данните в профила в {{ config('app.name') }}</title>
</head>

<body style="font-family: Arial, sans-serif; line-height:1.5; color:#222;">

<p>Здравейте, {{ $user->first_name }} {{ $user->last_name }},</p>

<p>Информираме Ви, че данните във Вашия профил в
    <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>
    бяха успешно актуализирани на <strong>{{ $date }}</strong>.
</p>

<p>Поздрави,<br>{{ config('app.name') }}</p>

</body>
</html>
