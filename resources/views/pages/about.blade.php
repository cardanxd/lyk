@extends('layouts.public')

@section('title', 'Nosotros')

@section('content')
    @include('partials.page-header', ['eyebrow' => 'Acerca de nosotros', 'title' => setting('site_name', 'LyK Asesoría y Consultoría de Pensiones y AFORE')])

    <section class="container-page grid items-center gap-10 py-14 md:grid-cols-2 md:py-20">
        <img src="{{ app(\App\Support\Settings::class)->imageUrl('about_image_path', 'images/brand/portada.jpg') }}" alt="Equipo de LyK Asesoría" class="aspect-[4/3] w-full rounded-3xl object-cover shadow-xl shadow-navy/10">
        <div>
            <h2 class="mb-4 text-3xl">Nuestra historia</h2>
            <div class="text-lg text-muted">{!! nl2br(e(setting('history', ''))) !!}</div>
        </div>
    </section>

    <section class="bg-white py-14 md:py-20">
        <div class="container-page grid gap-6 md:grid-cols-2">
            @foreach ([['Misión', 'mission', 'heart'], ['Visión', 'vision', 'shield']] as [$label, $key, $icon])
                @if ($text = setting($key))
                    <div class="rounded-3xl border border-sage bg-cream p-8">
                        <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-navy text-gold"><x-icon :name="$icon" class="h-7 w-7" /></span>
                        <h2 class="mb-3 text-2xl">{{ $label }}</h2>
                        <p class="text-lg">{!! nl2br(e($text)) !!}</p>
                    </div>
                @endif
            @endforeach
        </div>
    </section>

    @php($values = array_values(array_filter(array_map('trim', preg_split('/\R/', (string) setting('values'))))))
    @if ($values)
        <section class="py-14 md:py-20" aria-labelledby="values-title">
            <div class="container-page">
                <h2 id="values-title" class="mb-8 text-center text-3xl">Nuestros valores</h2>
                <ul class="flex flex-wrap justify-center gap-3">
                    @foreach ($values as $value)
                        <li class="flex items-center gap-2 rounded-full bg-white px-5 py-3 text-lg font-semibold text-navy shadow-sm shadow-navy/5">
                            <svg class="h-5 w-5 text-leaf" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" d="M5 12l5 5 9-10"/></svg>
                            {{ $value }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @include('partials.cta')
@endsection
