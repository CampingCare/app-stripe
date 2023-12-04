<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stripe App</title>
    @vite('resources/css/app.css')
</head>
<body>
    <div>
        {{ $slot }}
    </div>

    <script src="{{ asset('js/widgets.js') }}"></script>
</body>
</html>
