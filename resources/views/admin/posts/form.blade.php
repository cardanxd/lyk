@extends('layouts.admin')

@section('title', $post->exists ? 'Editar post' : 'Nuevo post')

@push('head')
    @vite('resources/js/admin.js')
@endpush

@section('actions')
    <a href="{{ route('admin.posts.index') }}" class="text-leaf-700 underline">← Volver a posts</a>
@endsection

@section('content')
    <form method="POST" enctype="multipart/form-data"
          action="{{ $post->exists ? route('admin.posts.update', $post->id) : route('admin.posts.store') }}"
          class="grid gap-6 lg:grid-cols-3">
        @csrf
        @if ($post->exists) @method('PUT') @endif

        <div class="space-y-5 rounded-2xl bg-white p-6 lg:col-span-2">
            <x-admin.field name="title" label="Título" :value="$post->title" required />
            <x-admin.field name="excerpt" label="Resumen corto" :value="$post->excerpt" rows="2" maxlength="300" help="Se muestra en la lista de posts y al compartir. Si lo dejas vacío se toma el inicio del texto." />
            <div>
                <label for="body-editor" class="form-label">Contenido</label>
                <input id="body" type="hidden" name="body" value="{{ old('body', $post->editorBody()) }}">
                <trix-editor id="body-editor" input="body" class="prose-lyk min-h-80 rounded-lg border border-gray-300 bg-white"></trix-editor>
                @error('body')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <section aria-labelledby="gallery-title" class="space-y-5 border-t border-sage pt-6">
                <div>
                    <h2 id="gallery-title" class="text-xl">Galería</h2>
                    <p class="form-help">Fotos y videos que aparecen debajo del texto del post.</p>
                </div>

                @if ($post->exists && $post->media->isNotEmpty())
                    <ul class="grid gap-4 sm:grid-cols-2">
                        @foreach ($post->media as $media)
                            <li class="rounded-xl border border-sage p-3">
                                <div class="mb-3 flex aspect-video items-center justify-center overflow-hidden rounded-lg bg-sage-100">
                                    @if ($media->type === 'image')
                                        <img src="{{ $media->publicUrl() }}" alt="" class="h-full w-full object-cover">
                                    @elseif ($media->type === 'video')
                                        <video src="{{ $media->publicUrl() }}" preload="metadata" muted class="h-full w-full object-cover"></video>
                                    @else
                                        <span class="font-semibold text-navy">Video de {{ str_contains($media->url, 'facebook') ? 'Facebook' : 'YouTube' }}</span>
                                    @endif
                                </div>
                                <label for="media-{{ $media->id }}-caption" class="form-label text-sm">Descripción</label>
                                <input id="media-{{ $media->id }}-caption" name="media[{{ $media->id }}][caption]" value="{{ old("media.{$media->id}.caption", $media->caption) }}" class="form-input mb-2" maxlength="200">
                                <div class="flex items-end gap-2">
                                    <div class="w-24">
                                        <label for="media-{{ $media->id }}-order" class="form-label text-sm">Orden</label>
                                        <input id="media-{{ $media->id }}-order" name="media[{{ $media->id }}][sort_order]" type="number" min="0" value="{{ old("media.{$media->id}.sort_order", $media->sort_order) }}" class="form-input">
                                    </div>
                                    <button form="delete-media-{{ $media->id }}" class="btn ml-auto min-h-11 border-2 border-red-700 px-4 py-2 text-red-700 hover:bg-red-700 hover:text-white">Quitar</button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div>
                    <label for="gallery_images" class="form-label">Agregar fotos</label>
                    <input id="gallery_images" name="gallery_images[]" type="file" multiple accept="image/jpeg,image/png,image/webp" class="block w-full cursor-pointer rounded-lg border border-gray-300 bg-white text-base file:mr-4 file:min-h-11 file:cursor-pointer file:border-0 file:bg-sage-100 file:px-4 file:font-semibold file:text-navy" aria-describedby="gallery_images-help">
                    <p id="gallery_images-help" class="form-help">Hasta 10 a la vez. JPG, PNG o WEBP, máx. 5 MB cada una.</p>
                    @error('gallery_images')<p class="form-error">{{ $message }}</p>@enderror
                    @foreach ($errors->get('gallery_images.*') as $messages)<p class="form-error">{{ $messages[0] }}</p>@endforeach
                </div>
                <div>
                    <label for="gallery_videos" class="form-label">Agregar videos cortos</label>
                    <input id="gallery_videos" name="gallery_videos[]" type="file" multiple accept="video/mp4,video/quicktime,video/webm" class="block w-full cursor-pointer rounded-lg border border-gray-300 bg-white text-base file:mr-4 file:min-h-11 file:cursor-pointer file:border-0 file:bg-sage-100 file:px-4 file:font-semibold file:text-navy" aria-describedby="gallery_videos-help">
                    <p id="gallery_videos-help" class="form-help">Hasta 3 a la vez. MP4, MOV o WEBM, máx. 50 MB cada uno. En iPhone, el formato “Más compatible” (Ajustes → Cámara → Formatos) se reproduce en todos los navegadores.</p>
                    @error('gallery_videos')<p class="form-error">{{ $message }}</p>@enderror
                    @foreach ($errors->get('gallery_videos.*') as $messages)<p class="form-error">{{ $messages[0] }}</p>@endforeach
                </div>
                <x-admin.field name="video_url" label="O pega un enlace de YouTube o Facebook" type="text" placeholder="https://youtu.be/… o https://www.facebook.com/…/videos/…"
                    help="El video debe ser público. De Facebook, abre el video y copia la dirección completa de la barra del navegador (contiene /videos/ o /reel/), o en el video usa ··· → Insertar y pega el código. Los enlaces cortos de “Compartir” (/share/…) no funcionan." />
            </section>
        </div>

        <aside class="space-y-5 self-start rounded-2xl bg-white p-6">
            <fieldset>
                <legend class="form-label">Estado</legend>
                @foreach (['draft' => 'Borrador (no se ve en el sitio)', 'published' => 'Publicado'] as $value => $label)
                    <label class="mb-2 flex items-center gap-3">
                        <input type="radio" name="status" value="{{ $value }}" class="h-5 w-5 accent-leaf" @checked(old('status', $post->status) === $value)>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
                @error('status')<p class="form-error">{{ $message }}</p>@enderror
            </fieldset>
            <x-admin.field name="published_at" label="Fecha de publicación" type="datetime-local"
                :value="$post->published_at?->format('Y-m-d\TH:i')"
                help="Vacío = ahora. Una fecha futura programa la publicación." />
            <x-admin.image-field name="cover" label="Imagen de portada"
                :current-url="$post->cover_path ? storage_url($post->cover_path) : null" />
            <button class="btn-primary w-full">Guardar post</button>
        </aside>
    </form>

    @if ($post->exists)
        @foreach ($post->media as $media)
            <form id="delete-media-{{ $media->id }}" method="POST" action="{{ route('admin.posts.media.destroy', [$post->id, $media->id]) }}" onsubmit="return confirm('¿Quitar este elemento de la galería? Los cambios sin guardar del post se perderán; guárdalos antes si los necesitas.')" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    @endif
@endsection
