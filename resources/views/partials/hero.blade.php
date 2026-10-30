{{-- Carrusel de portadas. Sin portadas activas se muestra la portada de marca. --}}
@php
    $heroSlides = $slides->isNotEmpty()
        ? $slides
        : collect([(object) ['image_url' => asset('images/brand/portada.jpg'), 'title' => null, 'subtitle' => null, 'button_text' => null, 'button_url' => null]]);
@endphp
<section aria-roledescription="carrusel" aria-label="Portadas" class="relative bg-sage" data-carousel>
    <div class="flex snap-x snap-mandatory overflow-x-auto scroll-smooth [scrollbar-width:none]" data-track tabindex="-1">
        @foreach ($heroSlides as $slide)
            <div class="relative w-full shrink-0 snap-start" role="group" aria-roledescription="diapositiva" aria-label="{{ $loop->iteration }} de {{ $loop->count }}">
                <img src="{{ $slide->image_url ?? storage_url($slide->image_path) }}"
                     alt="{{ $slide->title ?: 'Asesoría de pensiones y AFORE para un retiro tranquilo' }}"
                     class="aspect-[3/2] max-h-[78vh] w-full object-cover md:aspect-[16/9] lg:aspect-[3/2]"
                     @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif>
                @if ($slide->title || $slide->subtitle)
                    <div class="absolute inset-0 flex items-end bg-gradient-to-t from-navy/85 via-navy/30 to-transparent">
                        <div class="container-page pb-8 md:pb-14">
                            @if ($slide->title)<h2 class="max-w-2xl text-3xl text-white md:text-5xl">{{ $slide->title }}</h2>@endif
                            @if ($slide->subtitle)<p class="mt-3 max-w-xl text-lg text-white/90 md:text-xl">{{ $slide->subtitle }}</p>@endif
                            @if ($slide->button_text && $slide->button_url)
                                <a href="{{ $slide->button_url }}" class="btn-primary mt-5">{{ $slide->button_text }}</a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    @if ($heroSlides->count() > 1)
        <div class="absolute bottom-4 left-4 flex items-center gap-2 rounded-full bg-white/90 p-1 shadow">
            <button type="button" class="flex h-11 w-11 cursor-pointer items-center justify-center rounded-full text-navy hover:bg-sage-100" data-prev aria-label="Portada anterior">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path stroke-linecap="round" d="M15 18l-6-6 6-6"/></svg>
            </button>
            <button type="button" class="flex h-11 w-11 cursor-pointer items-center justify-center rounded-full text-navy hover:bg-sage-100" data-toggle aria-label="Pausar">
                <svg class="h-5 w-5" data-icon-pause fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 5h3v14H7zM14 5h3v14h-3z"/></svg>
                <svg class="hidden h-5 w-5" data-icon-play fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5l11 7-11 7z"/></svg>
            </button>
            <button type="button" class="flex h-11 w-11 cursor-pointer items-center justify-center rounded-full text-navy hover:bg-sage-100" data-next aria-label="Portada siguiente">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path stroke-linecap="round" d="M9 6l6 6-6 6"/></svg>
            </button>
        </div>
        <p class="sr-only" aria-live="polite" data-status></p>
    @endif
</section>

<section class="border-b border-sage bg-white">
    <div class="container-page flex flex-col items-start gap-6 py-10 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="eyebrow mb-2">Asesoría en pensiones IMSS y AFORE</p>
            <h1 class="max-w-2xl text-3xl md:text-4xl">{{ setting('tagline', 'Hacemos de tu retiro una nueva oportunidad') }}</h1>
        </div>
        <div class="flex flex-wrap gap-3">
            @if (Route::has('contact'))<a href="{{ route('contact') }}" class="btn-primary">Agenda tu asesoría</a>@endif
            @if ($services->isNotEmpty())<a href="#servicios" class="btn-secondary">Ver servicios</a>@endif
        </div>
    </div>
</section>
