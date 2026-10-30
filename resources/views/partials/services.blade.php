@if ($services->isNotEmpty())
    <section id="servicios" class="scroll-mt-24 bg-white py-16 md:py-20" aria-labelledby="services-title">
        <div class="container-page">
            <div class="mx-auto mb-10 max-w-2xl text-center">
                <p class="eyebrow mb-2">Servicios</p>
                <h2 id="services-title" class="text-3xl md:text-4xl">¿En qué te podemos ayudar?</h2>
            </div>
            <ul class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($services as $service)
                    <li class="flex flex-col rounded-2xl border border-sage bg-cream p-6 transition-shadow duration-200 hover:shadow-lg hover:shadow-navy/10">
                        <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-navy text-gold"><x-icon :name="$service->icon" class="h-7 w-7" /></span>
                        <h3 class="mb-2 text-xl">{{ $service->title }}</h3>
                        <p class="text-muted">{{ $service->description }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
@endif
