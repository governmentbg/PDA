<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Успешна регистрация</title>
</head>
<body style="font-family: Arial, sans-serif; line-height:1.5; color:#222;">

<p>Здравейте, {{ $user->first_name }} {{ $user->last_name }},</p>

<p>Вашата регистрация на <a href="{{ config('app.url') }}">{{ config('app.url') }}</a> е успешна!</p>

<p>Вече можете да влезете в профила си с потребителско име <strong>{{ $user->username }}</strong>, чрез бутона по-долу.</p>

<a href="{{ $loginUrl }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
    Вход
</a>

<p>Или копирайте следния адрес във вашия браузър:</p>
<p>{{ $loginUrl }}</p>

<p>Поздрави,<br>{{ config('app.name') }}</p>

</body>
</html>
