@if ($faqs->isNotEmpty())
    <section class="py-16 md:py-20" aria-labelledby="faqs-title">
        <div class="container-page max-w-3xl">
            <div class="mb-10 text-center">
                <p class="eyebrow mb-2">Resolvemos tus dudas</p>
                <h2 id="faqs-title" class="text-3xl md:text-4xl">Preguntas frecuentes</h2>
            </div>
            <div class="space-y-3">
                @foreach ($faqs as $faq)
                    <details class="group rounded-2xl border border-sage bg-white">
                        <summary class="flex min-h-14 cursor-pointer list-none items-center justify-between gap-4 px-5 py-4 text-lg font-semibold text-navy">
                            {{ $faq->question }}
                            <svg class="h-6 w-6 shrink-0 text-leaf-700 transition-transform duration-200 group-open:rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                        </summary>
                        <div class="px-5 pb-5 text-muted">{!! nl2br(e($faq->answer)) !!}</div>
                    </details>
                @endforeach
            </div>
        </div>
    </section>
@endif
