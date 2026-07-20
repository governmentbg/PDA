<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Активиране на акаунт</title>
</head>
<body style="font-family: Arial, sans-serif; line-height:1.5; color:#222;">

<p>Здравейте, {{ $user->first_name }} {{ $user->last_name }},</p>

<p>Благодарим Ви, че се регистрирахте на <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>.
    За да активирате вашия акаунт, моля натиснете бутона по-долу.</p>

<a href="{{ $activationUrl }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
    Активирай акаунт
</a>

<p>Или копирайте следния адрес във вашия браузър:</p>
<p>{{ $activationUrl }}</p>

<p>Този линк ще бъде валиден в продължение на 30 минути.</p>

<p>Регистрацията е инициирана на {{ $registrationDate }}. Ако не Вие сте инициирали регистрацията, моля, игнорирайте този имейл.</p>

<p>Поздрави,<br>{{ config('app.name') }}</p>

</body>
</html>
