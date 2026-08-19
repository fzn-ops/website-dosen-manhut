<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DosenManhut - IPB University')</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen font-sans antialiased">

    <x-layouts.navbar />

    <main class="flex-grow">
        {{ $slot }}
    </main>

    <x-layouts.footer />

</body>
</html>