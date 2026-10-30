<section class="px-4 py-16 md:py-20" aria-labelledby="cta-title">
    <div class="container-page relative overflow-hidden rounded-3xl bg-navy px-6 py-12 text-center md:px-12 md:py-16">
        <svg class="pointer-events-none absolute -right-10 -bottom-10 h-64 w-64 text-white/5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2c3 0 6 2.5 6 6 2 .5 3 2 3 4 0 3-2.5 5-5 5h-3v5h-2v-5H8c-2.5 0-5-2-5-5 0-2 1-3.5 3-4 0-3.5 3-6 6-6z"/></svg>
        <h2 id="cta-title" class="relative mb-4 text-3xl text-white md:text-4xl">¿Listo para planear tu retiro?</h2>
        <p class="relative mx-auto mb-8 max-w-xl text-lg text-white/85">La primera plática es para conocer tu caso. Escríbenos y resolvemos tus dudas sin compromiso.</p>
        <div class="relative flex flex-wrap justify-center gap-3">
            @if ($wa = preg_replace('/\D/', '', (string) setting('whatsapp')))
                <a href="https://wa.me/{{ $wa }}?text={{ rawurlencode('Hola, me gustaría recibir asesoría sobre mi pensión.') }}" target="_blank" rel="noopener" class="btn-primary">Escríbenos por WhatsApp</a>
            @endif
            @if (Route::has('contact'))<a href="{{ route('contact') }}" class="btn-light">Enviar un mensaje</a>@endif
            @if ($phone = setting('phone'))<a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="btn-light">Llamar al {{ $phone }}</a>@endif
        </div>
    </div>
</section>
