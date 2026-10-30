@if ($testimonials->isNotEmpty())
    <section class="bg-sage-100 py-16 md:py-20" aria-labelledby="testimonials-title">
        <div class="container-page">
            <div class="mx-auto mb-10 max-w-2xl text-center">
                <p class="eyebrow mb-2">Testimonios</p>
                <h2 id="testimonials-title" class="text-3xl md:text-4xl">Lo que dicen nuestros clientes</h2>
            </div>
            <ul class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($testimonials as $testimonial)
                    <li>
                        <figure class="flex h-full flex-col rounded-2xl bg-white p-6">
                            <svg class="mb-3 h-8 w-8 text-gold" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 7h4v4H8c0 2 1 3 3 3v3c-4 0-6-2-6-6V7zm9 0h4v4h-3c0 2 1 3 3 3v3c-4 0-6-2-6-6V7z"/></svg>
                            <blockquote class="mb-5 flex-1 text-lg">{{ $testimonial->quote }}</blockquote>
                            <figcaption class="flex items-center gap-3">
                                @if ($testimonial->photo_path)
                                    <img src="{{ storage_url($testimonial->photo_path) }}" alt="" loading="lazy" class="h-12 w-12 rounded-full object-cover">
                                @else
                                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-navy font-serif text-lg text-white" aria-hidden="true">{{ mb_substr($testimonial->name, 0, 1) }}</span>
                                @endif
                                <span>
                                    <span class="block font-bold text-navy">{{ $testimonial->name }}</span>
                                    @if ($testimonial->location)<span class="text-sm text-muted">{{ $testimonial->location }}</span>@endif
                                </span>
                            </figcaption>
                        </figure>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
@endif
