<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Final Project</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <header class="w-full max-w-4xl mx-auto mt-6 px-6">
        <nav class="flex items-center justify-end gap-3">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="px-5 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="px-5 py-2 text-gray-700 border border-gray-300 rounded-md text-sm font-medium hover:bg-gray-100 transition">
                        Log in
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="px-5 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 transition">
                            Register
                        </a>
                    @endif
                @endauth
            @endif
        </nav>
    </header>

    <main class="flex-1 flex flex-col items-center justify-center -mt-16">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">placeholder</h1>
        <p class="text-gray-500 mb-8">My Final Project</p>
        <div class="flex gap-4">
            <a href="{{ url('/map') }}"
               class="px-6 py-3 bg-green-600 text-white rounded-md font-medium hover:bg-green-700 transition inline-flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
                Open Map
            </a>
        </div>
    </main>

    <footer class="text-center text-gray-400 text-sm py-6">
        &copy; {{ date('Y') }} My Final Project
        @include('partials.version')
    </footer>
</body>
</html>
