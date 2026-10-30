<section class="py-16 md:py-20" aria-labelledby="why-title">
    <div class="container-page grid items-center gap-10 md:grid-cols-2">
        <div>
            <p class="eyebrow mb-2">¿Por qué LyK?</p>
            <h2 id="why-title" class="mb-4 text-3xl md:text-4xl">Tu pensión es el resultado de toda una vida de trabajo</h2>
            <p class="text-lg text-muted">Una decisión mal informada puede costarte miles de pesos cada mes. Te explicamos tus opciones con palabras claras y te acompañamos en cada paso, para que te retires con tranquilidad.</p>
        </div>
        <ul class="grid gap-4">
            @foreach ([
                ['users', 'Asesoría personalizada', 'Revisamos tu historial y tu caso, no te damos respuestas genéricas.'],
                ['shield', 'Información actualizada', 'Seguimos los cambios del IMSS y del DOF para que decidas con datos vigentes.'],
                ['heart', 'Acompañamiento completo', 'Desde el primer diagnóstico hasta que recibes tu pensión.'],
            ] as [$icon, $title, $text])
                <li class="flex gap-4 rounded-2xl bg-white p-5 shadow-sm shadow-navy/5">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-sage-100 text-leaf-700"><x-icon :name="$icon" /></span>
                    <div>
                        <h3 class="font-sans text-lg font-bold">{{ $title }}</h3>
                        <p class="text-muted">{{ $text }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</section>
