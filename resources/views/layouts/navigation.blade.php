<nav x-data="{ open: false }" class="bg-[var(--color-header-bg)] dark:bg-[var(--color-header-bg)] border-b border-[var(--color-header-border)] dark:border-[var(--color-header-border)]">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center gap-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--color-shield)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="shield-glow">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="M9 12l2 2 4-4"/>
                        </svg>
                        <span class="font-bold text-[var(--color-text)] dark:text-[var(--color-text)]">MaiGuard</span>
                        <span class="text-[10px] font-semibold uppercase tracking-wider px-1.5 py-0.5 rounded-full bg-[var(--color-accent-soft)] dark:bg-[var(--color-accent-soft)] text-[var(--color-accent-text)] dark:text-[var(--color-accent-text)]">Keamanan</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="url('/map')" :active="request()->is('map')">
                        Map
                    </x-nav-link>
                    <x-nav-link :href="url('/table')" :active="request()->is('table')">
                        Table
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-[var(--color-text-secondary)] dark:text-[var(--color-text-secondary)] bg-transparent hover:text-[var(--color-text)] dark:hover:text-[var(--color-text)] hover:bg-[var(--color-surface-hover)] dark:hover:bg-[var(--color-surface-hover)] focus:outline-none transition ease-in-out duration-150">
                            @include('partials.user-avatar', ['size' => 28])
                            <div class="ms-2">{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-[var(--color-text-muted)] dark:text-[var(--color-text-muted)] hover:text-[var(--color-text)] dark:hover:text-[var(--color-text)] hover:bg-[var(--color-surface-hover)] dark:hover:bg-[var(--color-surface-hover)] focus:outline-none focus:bg-[var(--color-surface-hover)] dark:focus:bg-[var(--color-surface-hover)] focus:text-[var(--color-text)] dark:focus:text-[var(--color-text)] transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="url('/map')" :active="request()->is('map')">
                Map
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="url('/table')" :active="request()->is('table')">
                Table
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-[var(--color-border)] dark:border-[var(--color-border)]">
            <div class="px-4">
                <div class="font-medium text-base text-[var(--color-text)] dark:text-[var(--color-text)]">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-[var(--color-text-secondary)] dark:text-[var(--color-text-secondary)]">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
