<section class="bg-sage-100">
    <div class="container-page py-12 md:py-16">
        @isset($eyebrow)<p class="eyebrow mb-2">{{ $eyebrow }}</p>@endisset
        <h1 class="max-w-3xl text-4xl md:text-5xl">{{ $title }}</h1>
        @isset($lead)<p class="mt-4 max-w-2xl text-lg text-muted">{{ $lead }}</p>@endisset
    </div>
</section>
