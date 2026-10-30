@props(['name'])
@php
    $paths = [
        'piggy-bank' => 'M19 10c0-3.3-3.1-6-7-6S5 6.7 5 10c0 1.9 1 3.6 2.6 4.7V18h3v-2h2.8v2h3v-3.3c.9-.6 1.6-1.4 2.1-2.4H20v-2.3h-1zM12 4V2M15 9h.01',
        'document' => 'M7 3h7l5 5v13H7zM14 3v5h5M10 13h6M10 17h6',
        'calendar' => 'M4 6h16v14H4zM4 10h16M8 3v4M16 3v4M8 14h2M14 14h2M8 17h2',
        'shield' => 'M12 3l8 3v6c0 4.5-3.4 8.3-8 9-4.6-.7-8-4.5-8-9V6zM9 12l2 2 4-4',
        'users' => 'M16 19v-1a4 4 0 00-4-4H6a4 4 0 00-4 4v1M9 10a3 3 0 100-6 3 3 0 000 6zM22 19v-1a4 4 0 00-3-3.9M16 4.1a3 3 0 010 5.8',
        'scale' => 'M12 3v18M7 21h10M5 7h14M5 7l-3 7a3 3 0 006 0zM19 7l-3 7a3 3 0 006 0z',
        'heart' => 'M20.8 5.6a5.5 5.5 0 00-7.8 0L12 6.7l-1-1.1a5.5 5.5 0 00-7.8 7.8L12 22l8.8-8.6a5.5 5.5 0 000-7.8z',
    ];
@endphp
<svg {{ $attributes->merge(['class' => 'h-6 w-6']) }} fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $paths[$name] ?? $paths['document'] }}"/></svg>
