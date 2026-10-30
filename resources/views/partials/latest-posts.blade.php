@if ($posts->isNotEmpty())
    <section class="bg-white py-16 md:py-20" aria-labelledby="posts-title">
        <div class="container-page">
            <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="eyebrow mb-2">Guías y noticias</p>
                    <h2 id="posts-title" class="text-3xl md:text-4xl">Infórmate antes de decidir</h2>
                </div>
                <a href="{{ route('posts.index') }}" class="btn-secondary">Ver todos los posts</a>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>
        </div>
    </section>
@endif
