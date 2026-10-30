@php
    $nav = [
        ['route' => 'home', 'label' => 'Inicio'],
        ['route' => 'posts.index', 'label' => 'Posts'],
        ['route' => 'branches', 'label' => 'Sucursales'],
        ['route' => 'about', 'label' => 'Nosotros'],
        ['route' => 'contact', 'label' => 'Contacto'],
    ];
    $nav = array_filter($nav, fn ($item) => Route::has($item['route']) && ($item['route'] !== 'branches' || ($hasBranches ?? false)));
@endphp
<header class="sticky top-0 z-40 border-b border-sage/60 bg-cream/95 backdrop-blur">
    <div class="container-page flex items-center justify-between gap-4 py-3">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <img src="{{ app(\App\Support\Settings::class)->imageUrl('logo_path', 'images/brand/logo.png') }}" alt="LyK Asesoría y Consultoría de Pensiones y AFORE" class="h-14 w-auto" width="56" height="56">
            <span class="hidden font-serif text-lg leading-tight text-navy sm:block">LyK Asesoría<br><span class="text-sm text-muted font-sans">Pensiones y AFORE</span></span>
        </a>

        <nav aria-label="Principal" class="hidden md:block">
            <ul class="flex items-center gap-1">
                @foreach ($nav as $item)
                    <li>
                        <a href="{{ route($item['route']) }}"
                           @class([
                               'rounded-full px-4 py-2 font-semibold transition-colors duration-200 hover:bg-sage-100',
                               'text-leaf-700' => request()->routeIs($item['route'].'*'),
                               'text-navy' => ! request()->routeIs($item['route'].'*'),
                           ])
                           @if (request()->routeIs($item['route'])) aria-current="page" @endif>{{ $item['label'] }}</a>
                    </li>
                @endforeach
                @if (Route::has('contact'))
                    <li class="ml-2"><a href="{{ route('contact') }}" class="btn-primary">Agenda tu asesoría</a></li>
                @endif
            </ul>
        </nav>

        <details class="group relative md:hidden">
            <summary class="flex min-h-12 min-w-12 cursor-pointer list-none items-center justify-center rounded-full text-navy hover:bg-sage-100" aria-label="Abrir menú">
                <svg class="h-7 w-7 group-open:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg class="hidden h-7 w-7 group-open:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
            </summary>
            <nav aria-label="Principal móvil" class="absolute right-0 mt-2 w-64 rounded-2xl border border-sage bg-white p-2 shadow-lg">
                <ul>
                    @foreach ($nav as $item)
                        <li><a href="{{ route($item['route']) }}" class="block rounded-xl px-4 py-3 text-lg font-semibold text-navy hover:bg-sage-100">{{ $item['label'] }}</a></li>
                    @endforeach
                </ul>
            </nav>
        </details>
    </div>
</header>
