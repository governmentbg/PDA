<!doctype html>
<html class="no-js" lang="bg">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>{{env('APP_NAME')}}</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!--[if IE]>
    <p class="browserupgrade">Използвате <strong>прекаленно стар</strong> браузър. Моля, <a
        href="https://browsehappy.com/">обновете го</a> за да повишите вашата сигурност.</p>
    <![endif]-->

    @include('partials.head')

    {{--Font Awesome--}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          referrerpolicy="no-referrer">
    @stack('css')
</head>
<body class="index-page d-flex flex-column min-vh-100">

    @include('partials.header')

<main class="main flex-fill">
    <!-- Main Content -->
@include('partials.content')
</main>

@include('partials.footer-visual')
@include('partials.footer')

@stack('scripts')
</body>
</html>
