@php
    $menu = [
        ['route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'label' => 'Inicio', 'icon' => 'M3 12l9-9 9 9M5 10v10h14V10'],
        ['route' => 'admin.settings.edit', 'match' => 'admin.settings.*', 'label' => 'Ajustes', 'icon' => 'M12 15a3 3 0 100-6 3 3 0 000 6zM19.4 15a1.7 1.7 0 00.3 1.8l.1.1a2 2 0 11-2.8 2.8l-.1-.1a1.7 1.7 0 00-1.8-.3 1.7 1.7 0 00-1 1.5V21a2 2 0 11-4 0v-.1a1.7 1.7 0 00-1.1-1.5 1.7 1.7 0 00-1.8.3l-.1.1a2 2 0 11-2.8-2.8l.1-.1a1.7 1.7 0 00.3-1.8 1.7 1.7 0 00-1.5-1H3a2 2 0 110-4h.1a1.7 1.7 0 001.5-1.1 1.7 1.7 0 00-.3-1.8l-.1-.1a2 2 0 112.8-2.8l.1.1a1.7 1.7 0 001.8.3H9a1.7 1.7 0 001-1.5V3a2 2 0 114 0v.1a1.7 1.7 0 001 1.5 1.7 1.7 0 001.8-.3l.1-.1a2 2 0 112.8 2.8l-.1.1a1.7 1.7 0 00-.3 1.8V9a1.7 1.7 0 001.5 1H21a2 2 0 110 4h-.1a1.7 1.7 0 00-1.5 1z'],
        ['route' => 'admin.branches.index', 'match' => 'admin.branches.*', 'label' => 'Sucursales', 'icon' => 'M12 22s7-6.5 7-12a7 7 0 10-14 0c0 5.5 7 12 7 12zM12 12a2 2 0 100-4 2 2 0 000 4z'],
        ['route' => 'admin.slides.index', 'match' => 'admin.slides.*', 'label' => 'Portadas', 'icon' => 'M4 5h16v14H4zM4 15l4-4 4 4 3-3 5 5'],
        ['route' => 'admin.posts.index', 'match' => 'admin.posts.*', 'label' => 'Posts', 'icon' => 'M7 3h7l5 5v13H7zM14 3v5h5M10 13h6M10 17h6'],
        ['route' => 'admin.services.index', 'match' => 'admin.services.*', 'label' => 'Servicios', 'icon' => 'M4 7h16v12H4zM9 7V4h6v3'],
        ['route' => 'admin.faqs.index', 'match' => 'admin.faqs.*', 'label' => 'Preguntas', 'icon' => 'M9 9a3 3 0 115 2c-1 1-2 1.5-2 3M12 18h.01M12 22a10 10 0 100-20 10 10 0 000 20z'],
        ['route' => 'admin.testimonials.index', 'match' => 'admin.testimonials.*', 'label' => 'Testimonios', 'icon' => 'M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z'],
        ['route' => 'admin.messages.index', 'match' => 'admin.messages.*', 'label' => 'Mensajes', 'icon' => 'M4 4h16v16H4zM4 6l8 7 8-7', 'badge' => $unreadMessages ?? 0],
        ['route' => 'admin.tags.index', 'match' => 'admin.tags.*', 'label' => 'Etiquetas', 'icon' => 'M20.6 13.4l-7.2 7.2a2 2 0 01-2.8 0L3 13V3h10l7.6 7.6a2 2 0 010 2.8zM7.5 7.5h.01'],
        ['route' => 'admin.password.edit', 'match' => 'admin.password.*', 'label' => 'Mi contraseña', 'icon' => 'M6 11h12v10H6zM8 11V7a4 4 0 118 0v4'],
    ];
    $menu = array_filter($menu, fn ($item) => Route::has($item['route']));
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('title', 'Panel') · Admin LyK</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-cream">
    <div class="md:flex">
        <aside class="bg-navy text-white md:sticky md:top-0 md:h-screen md:w-64 md:shrink-0 md:overflow-y-auto">
            <div class="flex items-center justify-between gap-3 px-5 py-4">
                <a href="{{ route('admin.dashboard') }}" class="font-serif text-xl">LyK <span class="text-gold">Admin</span></a>
                <a href="{{ route('home') }}" target="_blank" class="text-sm text-white/80 underline hover:text-gold">Ver sitio</a>
            </div>
            <nav aria-label="Panel" class="px-3 pb-4">
                <ul class="flex gap-1 overflow-x-auto md:flex-col">
                    @foreach ($menu as $item)
                        <li class="shrink-0">
                            <a href="{{ route($item['route']) }}" @class([
                                'flex min-h-11 items-center gap-3 rounded-lg px-3 py-2 font-semibold transition-colors duration-200',
                                'bg-white/15 text-gold' => request()->routeIs($item['match']),
                                'text-white/90 hover:bg-white/10' => ! request()->routeIs($item['match']),
                            ])>
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $item['icon'] }}"/></svg>
                                <span>{{ $item['label'] }}</span>
                                @if (! empty($item['badge']))
                                    <span class="ml-auto rounded-full bg-gold px-2 text-sm text-navy" aria-label="{{ $item['badge'] }} sin leer">{{ $item['badge'] }}</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                    <li class="shrink-0">
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button class="flex min-h-11 w-full cursor-pointer items-center gap-3 rounded-lg px-3 py-2 font-semibold text-white/90 hover:bg-white/10">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M15 3h4v18h-4M10 17l5-5-5-5M15 12H3"/></svg>
                                Salir
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="min-w-0 flex-1 px-4 py-6 sm:px-8 sm:py-8">
            <div class="mx-auto max-w-5xl">
                <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                    <h1 class="text-2xl sm:text-3xl">@yield('title', 'Panel')</h1>
                    @yield('actions')
                </div>

                @if (session('status'))
                    <div role="status" class="mb-6 rounded-xl border border-leaf/40 bg-sage-100 px-4 py-3 font-semibold text-leaf-700">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div role="alert" class="mb-6 rounded-xl border border-red-300 bg-red-50 px-4 py-3 text-red-800">
                        <p class="font-semibold">Revisa los campos marcados.</p>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
