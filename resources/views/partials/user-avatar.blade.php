{{-- Avatar image partial --}}
@props(['user' => null, 'size' => 32])
@php
    $user = $user ?? Auth::user();
    $fontSize = $size >= 36 ? 15 : ($size >= 28 ? 12 : 10);
@endphp

@if($user && $user->avatar)
    <img src="{{ url('/files/' . $user->avatar) }}" alt="{{ $user->name }}"
         style="width:{{ $size }}px; height:{{ $size }}px; border-radius:50%; object-fit:cover; flex-shrink:0;"
         onerror="this.style.display='none'">
@elseif($user)
    <span style="width:{{ $size }}px; height:{{ $size }}px; border-radius:50%; flex-shrink:0; background:var(--color-accent-soft); color:var(--color-accent-text); display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:{{ $fontSize }}px; text-transform:uppercase;">
        {{ strtoupper(substr($user->name, 0, 2)) }}
    </span>
@endif
