<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[var(--color-text)] dark:text-[var(--color-text)] leading-tight flex items-center gap-2">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-shield)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shield-glow">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[var(--color-surface)] dark:bg-[var(--color-surface)] border border-[var(--color-border)] dark:border-[var(--color-border)] shadow-sm sm:rounded-lg">
                <div class="p-6 text-[var(--color-text)] dark:text-[var(--color-text)]">
                    <p class="text-lg font-semibold mb-2">{{ __("You're logged in!") }}</p>
                    <p class="text-[var(--color-text-secondary)] dark:text-[var(--color-text-secondary)] text-sm">Welcome to the wow very bad guy is here Geo Dashboard. Navigate to the map to begin monitoring geospatial security features.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
