@if ($branches->isNotEmpty())
    <section class="py-16 md:py-20" aria-labelledby="branches-title">
        <div class="container-page">
            <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="eyebrow mb-2">Nuestras sucursales</p>
                    <h2 id="branches-title" class="text-3xl md:text-4xl">Contamos con {{ $branches->count() }} {{ $branches->count() === 1 ? 'sucursal' : 'sucursales' }} para atenderte</h2>
                </div>
                <a href="{{ route('branches') }}" class="btn-secondary">Ver {{ $branches->count() > 1 ? 'todas' : 'detalles' }}</a>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($branches->take(3) as $branch)
                    <x-branch-card :branch="$branch" />
                @endforeach
            </div>
        </div>
    </section>
@endif
