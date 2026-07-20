<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Възстановяване на паролата за профила в {{ config('app.name') }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height:1.5; color:#222;">

<p>Здравейте, {{ $user->first_name }} {{ $user->last_name }}!</p>

<p>Получихме заявка за възстановяване на паролата за профила Ви
    <strong>{{ $user->username ?? $user->email }}</strong>
    на <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>.
    За да зададете нова парола, моля натиснете бутона по-долу.</p>

<a href="{{ $url }}" style="background-color:#4CAF50;color:#fff;padding:10px 20px;text-decoration:none;border-radius:5px;display:inline-block;">
    Промяна на парола
</a>

<p>Или копирайте следния адрес във вашия браузър:</p>
<p>{{ $url }}</p>

<p>Този линк ще бъде валиден в продължение на 30 минути.</p>

<p>Ако не Вие сте инициирали заявката за промяна на паролата, моля, игнорирайте този имейл.</p>

<p>Поздрави,<br>{{ config('app.name') }}</p>

</body>
</html>
