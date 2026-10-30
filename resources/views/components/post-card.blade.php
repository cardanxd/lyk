@props(['post'])

<article class="group flex flex-col overflow-hidden rounded-2xl border border-sage bg-white transition-shadow duration-200 hover:shadow-lg hover:shadow-navy/10">
    <a href="{{ route('posts.show', $post) }}" class="block overflow-hidden" tabindex="-1" aria-hidden="true">
        @if ($post->cover_path)
            <img src="{{ storage_url($post->cover_path) }}" alt="" loading="lazy" class="aspect-[4/3] w-full object-cover object-top transition-transform duration-300 group-hover:scale-[1.03]">
        @else
            <div class="flex aspect-[4/3] items-center justify-center bg-sage-100">
                <img src="{{ asset('images/brand/logo.png') }}" alt="" class="h-24 w-auto opacity-70">
            </div>
        @endif
    </a>
    <div class="flex flex-1 flex-col p-5">
        <time datetime="{{ $post->published_at->toDateString() }}" class="mb-2 text-sm text-muted">{{ $post->published_at->translatedFormat('j \d\e F \d\e Y') }}</time>
        <h3 class="mb-2 text-xl"><a href="{{ route('posts.show', $post) }}" class="hover:text-leaf-700">{{ $post->title }}</a></h3>
        <p class="mb-4 flex-1 text-muted">{{ $post->excerptText() }}</p>
        <a href="{{ route('posts.show', $post) }}" class="font-semibold text-leaf-700 underline-offset-4 hover:underline" aria-label="Leer {{ $post->title }}">Leer más →</a>
    </div>
</article>
