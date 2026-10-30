@props(['branch'])

<article class="flex flex-col overflow-hidden rounded-2xl border border-sage bg-white">
    @if ($branch->photo_path)
        <img src="{{ storage_url($branch->photo_path) }}" alt="Fachada de {{ $branch->name }}" loading="lazy" class="aspect-[4/3] w-full object-cover">
    @else
        <div class="flex aspect-[4/3] items-center justify-center bg-sage-100 text-leaf-700" aria-hidden="true">
            <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M3 21h18M5 21V8l7-5 7 5v13M9 21v-6h6v6"/></svg>
        </div>
    @endif
    <div class="flex flex-1 flex-col p-6">
        <h3 class="mb-1 text-xl">{{ $branch->name }}</h3>
        @if ($branch->city)<p class="mb-3 text-sm font-semibold tracking-wide text-leaf-700 uppercase">{{ $branch->city }}</p>@endif
        <dl class="mb-5 flex-1 space-y-2 text-muted">
            <div class="flex gap-2"><dt class="sr-only">Dirección</dt>
                <svg class="mt-1 h-5 w-5 shrink-0 text-navy" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 22s7-6.5 7-12a7 7 0 10-14 0c0 5.5 7 12 7 12zM12 12a2 2 0 100-4 2 2 0 000 4z"/></svg>
                <dd>{{ $branch->address }}</dd></div>
            @if ($branch->schedule)
                <div class="flex gap-2"><dt class="sr-only">Horario</dt>
                    <svg class="mt-1 h-5 w-5 shrink-0 text-navy" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 22a10 10 0 100-20 10 10 0 000 20zM12 6v6l4 2"/></svg>
                    <dd>{{ $branch->schedule }}</dd></div>
            @endif
            @if ($branch->phone)
                <div class="flex gap-2"><dt class="sr-only">Teléfono</dt>
                    <svg class="mt-1 h-5 w-5 shrink-0 text-navy" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1.9.4 1.8.7 2.7a2 2 0 01-.5 2.1L8 9.8a16 16 0 006 6l1.3-1.3a2 2 0 012.1-.4c.9.3 1.8.6 2.7.7a2 2 0 011.7 2z"/></svg>
                    <dd><a href="tel:{{ preg_replace('/[^\d+]/', '', $branch->phone) }}" class="text-leaf-700 underline-offset-4 hover:underline">{{ $branch->phone }}</a></dd></div>
            @endif
            @if ($branch->email)
                <div class="flex gap-2"><dt class="sr-only">Correo</dt>
                    <svg class="mt-1 h-5 w-5 shrink-0 text-navy" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 4h16v16H4zM4 6l8 7 8-7"/></svg>
                    <dd><a href="mailto:{{ $branch->email }}" class="break-all text-leaf-700 underline-offset-4 hover:underline">{{ $branch->email }}</a></dd></div>
            @endif
        </dl>
        <div class="flex flex-wrap gap-2">
            @if ($branch->maps_url)
                <a href="{{ $branch->maps_url }}" target="_blank" rel="noopener" class="btn-primary min-h-11 px-5 py-2">Cómo llegar</a>
            @endif
            @if ($wa = preg_replace('/\D/', '', (string) $branch->whatsapp))
                <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener" class="btn-secondary min-h-11 px-5 py-2">WhatsApp</a>
            @endif
        </div>
    </div>
</article>
