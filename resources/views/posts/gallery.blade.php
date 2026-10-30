@php
    $photos = $media->where('type', 'image')->values();
    $videos = $media->where('type', '!=', 'image');
    $lightboxItems = $photos->map(fn ($photo, $i) => [
        'src' => $photo->publicUrl(),
        'alt' => $photo->caption ?: 'Foto '.($i + 1).' de '.$post->title,
        'caption' => $photo->caption,
    ])->all();
@endphp
<section class="mt-12" aria-labelledby="gallery-title" data-gallery>
    <h2 id="gallery-title" class="mb-6 text-2xl">Galería</h2>

    @if ($photos->isNotEmpty())
        <ul class="grid grid-cols-2 gap-3 sm:grid-cols-3">
            @foreach ($photos as $i => $photo)
                <li>
                    <figure>
                        <button type="button" class="group block w-full cursor-zoom-in overflow-hidden rounded-xl" data-gallery-open="{{ $i }}" aria-label="Ver foto {{ $i + 1 }} de {{ $photos->count() }} en grande">
                            <img src="{{ $photo->publicUrl() }}" alt="{{ $photo->caption ?: 'Foto '.($i + 1).' de '.$post->title }}" loading="lazy" class="aspect-square w-full object-cover transition-transform duration-300 group-hover:scale-[1.04]">
                        </button>
                        @if ($photo->caption)<figcaption class="mt-1 text-sm text-muted">{{ $photo->caption }}</figcaption>@endif
                    </figure>
                </li>
            @endforeach
        </ul>

        <dialog class="m-auto h-full max-h-none w-full max-w-none bg-navy/95 p-0 backdrop:bg-navy/80" data-gallery-dialog aria-label="Fotos de {{ $post->title }}">
            <div class="flex h-full flex-col items-center justify-center gap-3 p-4">
                <img src="" alt="" class="max-h-[80vh] max-w-full rounded-lg object-contain" data-gallery-image>
                <p class="text-center text-white" data-gallery-caption></p>
                <div class="flex gap-3">
                    <button type="button" class="btn-light min-h-11 px-4 py-2" data-gallery-prev>← Anterior</button>
                    <button type="button" class="btn-primary min-h-11 px-4 py-2" data-gallery-close>Cerrar</button>
                    <button type="button" class="btn-light min-h-11 px-4 py-2" data-gallery-next>Siguiente →</button>
                </div>
            </div>
            <script type="application/json" data-gallery-items>{!! json_encode($lightboxItems, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) !!}</script>
        </dialog>
    @endif

    @if ($videos->isNotEmpty())
        <div class="mt-8 grid gap-6 md:grid-cols-2">
            @foreach ($videos as $video)
                <figure>
                    @if ($video->type === 'video')
                        <video src="{{ $video->publicUrl() }}" controls preload="metadata" playsinline class="aspect-video w-full rounded-xl bg-navy">
                            <a href="{{ $video->publicUrl() }}" download>Descargar el video</a>
                        </video>
                        <a href="{{ $video->publicUrl() }}" download class="mt-1 inline-block text-sm text-leaf-700 underline">¿No se reproduce? Descargar el video</a>
                    @else
                        @php($isFacebook = str_starts_with($video->url, 'https://www.facebook.com/'))
                        <iframe src="{{ $video->url }}" title="{{ $video->caption ?: 'Video de '.$post->title }}" loading="lazy"
                                data-embed="{{ $isFacebook ? 'facebook' : 'youtube' }}"
                                allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen
                                @class([
                                    'w-full rounded-xl border-0 bg-navy',
                                    'aspect-[9/16] max-w-sm mx-auto block' => $isFacebook,
                                    'aspect-video' => ! $isFacebook,
                                ])></iframe>
                    @endif
                    @if ($video->caption)<figcaption class="mt-1 text-muted">{{ $video->caption }}</figcaption>@endif
                </figure>
            @endforeach
        </div>
    @endif
</section>
