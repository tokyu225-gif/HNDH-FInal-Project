{{-- Security Theme (Keamanan) --}}
@php
    $theme = isset($appSettings) ? ($appSettings['theme'] ?? 'light') : 'light';
@endphp

<script>
    (function() {
        const theme = '{{ $theme }}';
        document.documentElement.setAttribute('data-theme', theme);
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    })();
</script>

<style>
    :root {
        /* ---- Light Theme (default) ---- */
        --color-bg: #f8fafc;
        --color-bg-secondary: #f1f5f9;
        --color-surface: #ffffff;
        --color-surface-hover: #f8fafc;
        --color-border: #e2e8f0;
        --color-border-light: #f1f5f9;
        --color-text: #1e293b;
        --color-text-secondary: #64748b;
        --color-text-muted: #94a3b8;
        --color-accent: #0284c7;
        --color-accent-hover: #0369a1;
        --color-accent-soft: #e0f2fe;
        --color-accent-text: #0369a1;
        --color-success: #059669;
        --color-success-soft: #d1fae5;
        --color-warning: #d97706;
        --color-warning-soft: #fef3c7;
        --color-danger: #dc2626;
        --color-danger-soft: #fee2e2;
        --color-purple: #7c3aed;
        --color-purple-soft: #ede9fe;
        --color-green: #059669;
        --color-green-soft: #d1fae5;
        --color-blue: #2563eb;
        --color-blue-soft: #dbeafe;
        --color-orange: #ea580c;
        --color-orange-soft: #ffedd5;

        /* Shadows */
        --shadow-sm: 0 1px 2px 0 rgba(0,0,0,0.05);
        --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1);

        /* Header/Nav */
        --color-header-bg: #ffffff;
        --color-header-border: #e2e8f0;
        --color-header-text: #1e293b;

        /* Table */
        --color-table-header-bg: #f8fafc;
        --color-table-row-hover: #f8fafc;
        --color-table-stripe: #f8fafc;

        /* Input */
        --color-input-bg: #ffffff;
        --color-input-border: #d1d5db;
        --color-input-text: #1e293b;
        --color-input-placeholder: #9ca3af;
        --color-input-focus-ring: #0284c7;

        /* Modal */
        --color-modal-overlay: rgba(0,0,0,0.5);
        --color-modal-bg: #ffffff;

        /* Security badge */
        --color-shield: #0284c7;
        --color-shield-glow: rgba(2, 132, 199, 0.15);

        /* Radius */
        --radius-sm: 6px;
        --radius-md: 8px;
        --radius-lg: 12px;
    }

    /* ---- Dark Theme (Keamanan / Security) ---- */
    [data-theme="dark"] {
        --color-bg: #0b1120;
        --color-bg-secondary: #111827;
        --color-surface: #1a2436;
        --color-surface-hover: #1f2d42;
        --color-border: #263148;
        --color-border-light: #1e293b;
        --color-text: #e2e8f0;
        --color-text-secondary: #94a3b8;
        --color-text-muted: #64748b;
        --color-accent: #06b6d4;
        --color-accent-hover: #22d3ee;
        --color-accent-soft: rgba(6, 182, 212, 0.12);
        --color-accent-text: #22d3ee;
        --color-success: #10b981;
        --color-success-soft: rgba(16, 185, 129, 0.12);
        --color-warning: #f59e0b;
        --color-warning-soft: rgba(245, 158, 11, 0.12);
        --color-danger: #ef4444;
        --color-danger-soft: rgba(239, 68, 68, 0.12);
        --color-purple: #a78bfa;
        --color-purple-soft: rgba(139, 92, 246, 0.12);
        --color-green: #34d399;
        --color-green-soft: rgba(52, 211, 153, 0.12);
        --color-blue: #60a5fa;
        --color-blue-soft: rgba(96, 165, 250, 0.12);
        --color-orange: #fb923c;
        --color-orange-soft: rgba(251, 146, 60, 0.12);

        --shadow-sm: 0 1px 2px 0 rgba(0,0,0,0.3);
        --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.4), 0 2px 4px -2px rgba(0,0,0,0.3);
        --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.5), 0 4px 6px -4px rgba(0,0,0,0.4);

        --color-header-bg: #151d2e;
        --color-header-border: #263148;
        --color-header-text: #e2e8f0;

        --color-table-header-bg: #151d2e;
        --color-table-row-hover: #1a2436;
        --color-table-stripe: #111827;

        --color-input-bg: #0f172a;
        --color-input-border: #334155;
        --color-input-text: #e2e8f0;
        --color-input-placeholder: #475569;
        --color-input-focus-ring: #06b6d4;

        --color-modal-overlay: rgba(0,0,0,0.7);
        --color-modal-bg: #1a2436;

        --color-shield: #06b6d4;
        --color-shield-glow: rgba(6, 182, 212, 0.2);
    }

    /* ---- Global Base Styles ---- */
    body {
        background-color: var(--color-bg);
        color: var(--color-text);
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* ---- Security Header Glow Effect (Dark only) ---- */
    [data-theme="dark"] .security-header {
        border-bottom: 1px solid var(--color-border);
        box-shadow: 0 1px 8px rgba(6, 182, 212, 0.08);
    }

    [data-theme="dark"] .surface-card {
        border: 1px solid var(--color-border);
        box-shadow: 0 2px 12px rgba(0,0,0,0.3);
    }

    [data-theme="dark"] .surface-card:hover {
        border-color: rgba(6, 182, 212, 0.25);
        box-shadow: 0 4px 20px rgba(6, 182, 212, 0.08);
    }

    /* ---- Security shield pulse ---- */
    @keyframes shieldPulse {
        0%, 100% { opacity: 0.6; }
        50% { opacity: 1; }
    }
    [data-theme="dark"] .shield-icon {
        animation: shieldPulse 3s ease-in-out infinite;
    }
</style>
