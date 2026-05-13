<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#047857">
        <link rel="manifest" href="/manifest.json">
        <title>@yield('title', 'Masuk Admin') - Lost & Found Campus</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100 text-gray-900 antialiased">
        <main class="flex min-h-screen items-center justify-center px-4 py-10">
            @yield('content')
        </main>
    </body>
</html>
