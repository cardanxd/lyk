@extends('layouts.public')

@section('title', $post->title)

@section('content')
    @php($shareUrl = route('posts.show', $post))
    <article>
        <header class="bg-sage-100">
            <div class="container-page max-w-3xl py-10 md:py-14">
                <a href="{{ route('posts.index') }}" class="mb-4 inline-block font-semibold text-leaf-700 underline-offset-4 hover:underline">← Todos los posts</a>
                <h1 class="mb-4 text-3xl md:text-5xl">{{ $post->title }}</h1>
                <time datetime="{{ $post->published_at->toDateString() }}" class="text-muted">{{ $post->published_at->translatedFormat('j \d\e F \d\e Y') }}</time>
            </div>
        </header>

        <div class="container-page max-w-3xl py-10">
            @if ($post->cover_path)
                <img src="{{ storage_url($post->cover_path) }}" alt="{{ $post->title }}" class="mb-8 w-full rounded-2xl shadow-lg shadow-navy/10">
            @endif

            <div class="prose-lyk">{!! $post->body !!}</div>

            <div class="mt-10 flex flex-wrap items-center gap-3 border-t border-sage pt-6">
                <span class="font-semibold text-navy">Compártelo:</span>
                <a href="https://wa.me/?text={{ rawurlencode($post->title.' '.$shareUrl) }}" target="_blank" rel="noopener" class="btn min-h-11 bg-[#1F8A4C] px-5 py-2 text-white hover:bg-[#176B3B]">WhatsApp</a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode($shareUrl) }}" target="_blank" rel="noopener" class="btn min-h-11 bg-[#1877F2] px-5 py-2 text-white hover:bg-[#0F5FCC]">Facebook</a>
            </div>
        </div>
    </article>

    @if ($related->isNotEmpty())
        <section class="bg-white py-14" aria-labelledby="related-title">
            <div class="container-page">
                <h2 id="related-title" class="mb-8 text-3xl">También te puede interesar</h2>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $item)
                        <x-post-card :post="$item" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @include('partials.cta')
@endsection
