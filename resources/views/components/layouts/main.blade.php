<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DosenManhut - IPB University')</title>
    @vite(['resources/css/app.css'])

    <!-- {{-- LaTeX Rendering Support (MathJax) --}} -->
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [['$', '$'], ['\\(', '\\)']],
                displayMath: [['$$', '$$'], ['\\[', '\\]']],
                processEscapes: true
            },
            options: {
                skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code']
            }
        };
    </script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen font-poppins antialiased">

    <x-layouts.navbar />

    <main class="flex-grow">
        {{ $slot }}
    </main>

    <x-layouts.footer />

</body>
</html>