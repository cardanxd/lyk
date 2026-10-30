@extends('layouts.public')

@section('title', 'Sucursales')
@section('description', 'Visítanos en cualquiera de nuestras sucursales de LyK Asesoría y Consultoría de Pensiones y AFORE.')

@section('content')
    @php($count = $branches->count())
    @include('partials.page-header', [
        'eyebrow' => 'Sucursales',
        'title' => $count ? 'Contamos con '.$count.' '.($count === 1 ? 'sucursal' : 'sucursales') : 'Nuestras sucursales',
        'lead' => $count ? 'Visítanos en la más cercana a ti. Te atendemos en persona con la misma calidad en cada una.' : null,
    ])

    <section class="container-page py-12 md:py-16">
        @if ($branches->isEmpty())
            <p class="rounded-2xl bg-white p-10 text-center text-lg text-muted">Muy pronto publicaremos nuestras sucursales. Mientras tanto, <a href="{{ route('contact') }}" class="font-semibold text-leaf-700 underline">escríbenos</a>.</p>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($branches as $branch)
                    <x-branch-card :branch="$branch" />
                @endforeach
            </div>

            @php($maps = $branches->filter(fn ($b) => $b->map_embed_url))
            @if ($maps->isNotEmpty())
                <h2 class="mt-16 mb-6 text-3xl">Ubicación en el mapa</h2>
                <div class="grid gap-6 md:grid-cols-2">
                    @foreach ($maps as $branch)
                        <figure>
                            <iframe src="{{ $branch->map_embed_url }}" title="Mapa de {{ $branch->name }}" class="aspect-[4/3] w-full rounded-2xl border-0 bg-sage-100" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                            <figcaption class="mt-2 font-semibold text-navy">{{ $branch->name }}</figcaption>
                        </figure>
                    @endforeach
                </div>
            @endif
        @endif
    </section>

    @include('partials.cta')
@endsection
