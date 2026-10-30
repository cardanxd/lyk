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
@endsection
