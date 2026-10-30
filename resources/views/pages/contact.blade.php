@extends('layouts.public')

@section('title', 'Contacto')

@section('content')
    @include('partials.page-header', [
        'eyebrow' => 'Contacto',
        'title' => 'Platícanos tu caso',
        'lead' => 'Déjanos tus datos y te llamamos. También puedes escribirnos por WhatsApp o visitarnos.',
    ])

    <section class="container-page grid gap-10 py-12 md:py-16 lg:grid-cols-5">
        <div class="lg:col-span-3">
            @if (session('status'))
                <div role="status" class="mb-6 flex gap-3 rounded-2xl border border-leaf/40 bg-sage-100 p-5 text-lg font-semibold text-leaf-700">
                    <svg class="h-7 w-7 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path stroke-linecap="round" d="M5 12l5 5 9-10"/></svg>
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('contact.store') }}" class="space-y-5 rounded-3xl bg-white p-6 shadow-sm shadow-navy/5 md:p-8" novalidate>
                @csrf
                @if ($errors->any())
                    <div role="alert" class="rounded-xl border border-red-300 bg-red-50 px-4 py-3 text-red-800">Revisa los campos marcados en rojo.</div>
                @endif

                <div class="hidden" aria-hidden="true">
                    <label for="website">No llenar este campo</label>
                    <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
                </div>

                <x-admin.field name="name" label="Nombre completo" required autocomplete="name" />
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.field name="phone" label="Teléfono o WhatsApp" type="tel" required autocomplete="tel" inputmode="tel" help="10 dígitos." />
                    <x-admin.field name="email" label="Correo (opcional)" type="email" autocomplete="email" />
                </div>
                <div>
                    <label for="topic" class="form-label">¿Sobre qué tema?</label>
                    <select id="topic" name="topic" class="form-input">
                        <option value="">Selecciona una opción</option>
                        @foreach ($topics as $topic)
                            <option value="{{ $topic }}" @selected(old('topic') === $topic)>{{ $topic }}</option>
                        @endforeach
                    </select>
                    @error('topic')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <x-admin.field name="message" label="Cuéntanos tu situación" rows="5" required />
                <div>
                    <label class="flex items-start gap-3">
                        <input type="checkbox" name="privacy" value="1" class="mt-1 h-6 w-6 shrink-0 rounded accent-leaf" @checked(old('privacy')) required>
                        <span>He leído y acepto el <a href="{{ route('privacy') }}" target="_blank" class="font-semibold text-leaf-700 underline">Aviso de privacidad</a>.</span>
                    </label>
                    @error('privacy')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <button class="btn-primary w-full sm:w-auto">Enviar mensaje</button>
            </form>
        </div>

        <aside class="space-y-4 lg:col-span-2">
            @php($wa = preg_replace('/\D/', '', (string) setting('whatsapp')))
            @foreach (array_filter([
                ['tel', 'Teléfono', setting('phone'), setting('phone') ? 'tel:'.preg_replace('/[^\d+]/', '', setting('phone')) : null],
                ['wa', 'WhatsApp', $wa ? setting('whatsapp') : null, $wa ? "https://wa.me/$wa" : null],
                ['mail', 'Correo', setting('email'), setting('email') ? 'mailto:'.setting('email') : null],
                ['pin', 'Dirección', setting('address'), null],
                ['clock', 'Horario', setting('schedule'), null],
            ], fn ($row) => filled($row[2])) as [$icon, $label, $value, $href])
                <div class="flex gap-4 rounded-2xl bg-white p-5">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-navy text-gold" aria-hidden="true">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ [
                            'tel' => 'M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1.9.4 1.8.7 2.7a2 2 0 01-.5 2.1L8 9.8a16 16 0 006 6l1.3-1.3a2 2 0 012.1-.4c.9.3 1.8.6 2.7.7a2 2 0 011.7 2z',
                            'wa' => 'M21 11.5a8.4 8.4 0 01-12.4 7.4L3 21l2.1-5.5A8.4 8.4 0 1121 11.5z',
                            'mail' => 'M4 4h16v16H4zM4 6l8 7 8-7',
                            'pin' => 'M12 22s7-6.5 7-12a7 7 0 10-14 0c0 5.5 7 12 7 12zM12 12a2 2 0 100-4 2 2 0 000 4z',
                            'clock' => 'M12 22a10 10 0 100-20 10 10 0 000 20zM12 6v6l4 2',
                        ][$icon] }}"/></svg>
                    </span>
                    <div class="min-w-0">
                        <p class="font-bold text-navy">{{ $label }}</p>
                        @if ($href)
                            <a href="{{ $href }}" @if ($icon === 'wa') target="_blank" rel="noopener" @endif class="break-words text-lg text-leaf-700 underline-offset-4 hover:underline">{{ $value }}</a>
                        @else
                            <p class="text-lg">{{ $value }}</p>
                        @endif
                    </div>
                </div>
            @endforeach

            @if ($branches->isNotEmpty())
                <div class="rounded-2xl bg-white p-5">
                    <p class="mb-3 font-bold text-navy">Nuestras sucursales</p>
                    <ul class="space-y-3">
                        @foreach ($branches as $branch)
                            <li>
                                <p class="font-semibold">{{ $branch->name }}</p>
                                <p class="text-muted">{{ $branch->address }}@if ($branch->city), {{ $branch->city }}@endif</p>
                                @if ($branch->maps_url)<a href="{{ $branch->maps_url }}" target="_blank" rel="noopener" class="text-leaf-700 underline">Cómo llegar</a>@endif
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('branches') }}" class="mt-4 inline-block font-semibold text-leaf-700 underline">Ver todas las sucursales</a>
                </div>
            @endif

            @if ($map = setting('map_embed_url'))
                <iframe src="{{ $map }}" title="Mapa de nuestra ubicación" class="aspect-[4/3] w-full rounded-2xl border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
            @endif
        </aside>
    </section>
@endsection
