<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Нова публикация в {{ $appName }}</title>
</head>

<body style="font-family: Arial, sans-serif; line-height:1.6; color:#222; background-color:#f8f8f8; padding:20px;">
<table width="100%" cellspacing="0" cellpadding="0" style="max-width:600px; margin:0 auto; background:#fff; border-radius:8px; padding:20px;">
    <tr>
        <td>
            <p>Здравейте, {{ $user->first_name }} {{ $user->last_name }}!</p>

            <p>Нова публикация:</p>

            <p><strong>{{ $article->title }}</strong></p>

            <br>

            <p style="font-size:0.9em; color:#555;">
                Ако не желаете да получавате повече такива имейли, можете да се откажете от абонамента си от настройките в профила си.
            </p>

            <p>Поздрави,<br>{{ $appName }}</p>
        </td>
    </tr>
</table>
</body>
</html>
