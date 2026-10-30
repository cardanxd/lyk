<section class="py-16 md:py-20" aria-labelledby="steps-title">
    <div class="container-page">
        <div class="mx-auto mb-10 max-w-2xl text-center">
            <p class="eyebrow mb-2">Cómo trabajamos</p>
            <h2 id="steps-title" class="text-3xl md:text-4xl">Tres pasos para planear tu retiro</h2>
        </div>
        <ol class="grid gap-6 md:grid-cols-3">
            @foreach ([
                ['Platícanos tu caso', 'Escríbenos por WhatsApp o el formulario. Te contactamos para conocer tu situación.'],
                ['Revisamos tu historial', 'Analizamos tus semanas cotizadas, tu régimen (Ley 73 o 97) y tu AFORE.'],
                ['Tu plan de pensión', 'Te presentamos opciones claras y te acompañamos en cada trámite.'],
            ] as $i => [$title, $text])
                <li class="relative rounded-2xl bg-white p-6 pt-10 shadow-sm shadow-navy/5">
                    <span class="absolute -top-5 left-6 flex h-11 w-11 items-center justify-center rounded-full bg-gold font-serif text-xl font-bold text-navy" aria-hidden="true">{{ $i + 1 }}</span>
                    <h3 class="mb-2 text-xl">{{ $title }}</h3>
                    <p class="text-muted">{{ $text }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>
