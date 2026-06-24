<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[var(--color-text)] dark:text-[var(--color-text)] leading-tight flex items-center gap-2">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-shield)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shield-glow">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            Dasbor
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Welcome --}}
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-[var(--color-text)]">Selamat datang, {{ Auth::user()->name }}</h3>
                <p class="text-sm text-[var(--color-text-secondary)] mt-1">Tinjau aktivitas keamanan dan pantau wilayah Lombok Nusra.</p>
            </div>

            {{-- Quick Stats --}}
            @php
                $spotCount = \App\Models\GeoPoint::count();
                $routeCount = \App\Models\GeoPolyline::count();
                $areaCount = \App\Models\GeoPolygon::count();
                $crimeTypes = \App\Models\GeoPoint::select('crime_type')->whereNotNull('crime_type')
                    ->union(\App\Models\GeoPolyline::select('crime_type')->whereNotNull('crime_type'))
                    ->union(\App\Models\GeoPolygon::select('crime_type')->whereNotNull('crime_type'))
                    ->distinct()->count();
                $recentSpots = \App\Models\GeoPoint::with('user')->latest()->limit(5)->get();
                $recentRoutes = \App\Models\GeoPolyline::with('user')->latest()->limit(3)->get();
                $recentAreas = \App\Models\GeoPolygon::with('user')->latest()->limit(3)->get();
            @endphp

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg p-4">
                    <div class="text-2xl font-bold text-[var(--color-blue)]">{{ $spotCount }}</div>
                    <div class="text-xs text-[var(--color-text-muted)] uppercase tracking-wider mt-1">Titik Lokasi</div>
                </div>
                <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg p-4">
                    <div class="text-2xl font-bold text-[var(--color-orange)]">{{ $routeCount }}</div>
                    <div class="text-xs text-[var(--color-text-muted)] uppercase tracking-wider mt-1">Rute Patroli</div>
                </div>
                <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg p-4">
                    <div class="text-2xl font-bold text-[var(--color-purple)]">{{ $areaCount }}</div>
                    <div class="text-xs text-[var(--color-text-muted)] uppercase tracking-wider mt-1">Zona Area</div>
                </div>
                <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg p-4">
                    <div class="text-2xl font-bold text-[var(--color-danger)]">{{ $crimeTypes }}</div>
                    <div class="text-xs text-[var(--color-text-muted)] uppercase tracking-wider mt-1">Jenis Kriminal</div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="flex flex-wrap gap-3 mb-8">
                <a href="{{ url('/map') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[var(--color-accent)] text-white rounded-md text-sm font-medium hover:bg-[var(--color-accent-hover)] transition no-underline">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="10" r="3"/><path d="M12 2a8 8 0 00-8 8c0 5.4 8 12 8 12s8-6.6 8-12a8 8 0 00-8-8z"/></svg>
                    Buka Peta
                </a>
                <a href="{{ url('/table') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-[var(--color-border)] text-[var(--color-text-secondary)] rounded-md text-sm font-medium hover:bg-[var(--color-surface-hover)] transition no-underline">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/></svg>
                    Lihat Data
                </a>
                <a href="{{ url('/profile') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-[var(--color-border)] text-[var(--color-text-secondary)] rounded-md text-sm font-medium hover:bg-[var(--color-surface-hover)] transition no-underline">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Profil
                </a>
            </div>

            {{-- Recent Activity --}}
            <div class="grid md:grid-cols-2 gap-6">
                {{-- Recent Spots --}}
                <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg p-5">
                    <h4 class="text-sm font-semibold text-[var(--color-text)] mb-3 flex items-center gap-2">
                        <span style="width:8px;height:8px;border-radius:50%;background:var(--color-blue);flex-shrink:0;"></span>
                        Titik Terbaru
                    </h4>
                    @forelse($recentSpots as $spot)
                        <div class="flex items-center justify-between py-2 border-b border-[var(--color-border-light)] last:border-0">
                            <div>
                                <div class="text-sm font-medium text-[var(--color-text)]">{{ $spot->name }}</div>
                                <div class="text-xs text-[var(--color-text-muted)]">{{ $spot->crime_type ?? 'Tidak diketahui' }} &middot; {{ $spot->created_at->diffForHumans() }}</div>
                            </div>
                            <span class="text-xs text-[var(--color-text-muted)]">{{ number_format($spot->latitude, 4) }}, {{ number_format($spot->longitude, 4) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-[var(--color-text-muted)]">Belum ada data titik.</p>
                    @endforelse
                </div>

                {{-- Recent Routes & Areas --}}
                <div class="space-y-6">
                    <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg p-5">
                        <h4 class="text-sm font-semibold text-[var(--color-text)] mb-3 flex items-center gap-2">
                            <span style="width:8px;height:8px;border-radius:50%;background:var(--color-orange);flex-shrink:0;"></span>
                            Rute Terbaru
                        </h4>
                        @forelse($recentRoutes as $route)
                            <div class="py-2 border-b border-[var(--color-border-light)] last:border-0">
                                <div class="text-sm font-medium text-[var(--color-text)]">{{ $route->name }}</div>
                                <div class="text-xs text-[var(--color-text-muted)]">{{ $route->crime_type ?? 'Tidak diketahui' }} &middot; {{ $route->created_at->diffForHumans() }}</div>
                            </div>
                        @empty
                            <p class="text-sm text-[var(--color-text-muted)]">Belum ada data rute.</p>
                        @endforelse
                    </div>

                    <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg p-5">
                        <h4 class="text-sm font-semibold text-[var(--color-text)] mb-3 flex items-center gap-2">
                            <span style="width:8px;height:8px;border-radius:50%;background:var(--color-purple);flex-shrink:0;"></span>
                            Zona Terbaru
                        </h4>
                        @forelse($recentAreas as $area)
                            <div class="py-2 border-b border-[var(--color-border-light)] last:border-0">
                                <div class="text-sm font-medium text-[var(--color-text)]">{{ $area->name }}</div>
                                <div class="text-xs text-[var(--color-text-muted)]">{{ $area->crime_type ?? 'Tidak diketahui' }} &middot; {{ $area->created_at->diffForHumans() }}</div>
                            </div>
                        @empty
                            <p class="text-sm text-[var(--color-text-muted)]">Belum ada data zona.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
