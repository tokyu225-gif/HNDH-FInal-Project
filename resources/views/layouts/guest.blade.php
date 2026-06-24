<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>wow very bad guy is here</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('partials.theme')
    </head>
    <body class="font-sans text-[var(--color-text)] dark:text-[var(--color-text)] antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[var(--color-bg)] dark:bg-[var(--color-bg)]">
            <div class="mb-4">
                <a href="/">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--color-shield)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="shield-glow"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-[var(--color-surface)] dark:bg-[var(--color-surface)] border border-[var(--color-border)] dark:border-[var(--color-border)] shadow-md sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
