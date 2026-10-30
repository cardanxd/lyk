@extends('layouts.admin')

@section('title', 'Posts')

@section('actions')
    <a href="{{ route('admin.posts.create') }}" class="btn-primary">+ Nuevo post</a>
@endsection

@section('content')
    <form method="GET" class="mb-5 flex flex-col gap-3 sm:flex-row" role="search">
        <label for="q" class="sr-only">Buscar por título</label>
        <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="Buscar por título" class="form-input sm:max-w-xs">
        <label for="status" class="sr-only">Estado</label>
        <select id="status" name="status" class="form-input sm:max-w-48">
            <option value="">Todos</option>
            <option value="published" @selected(request('status') === 'published')>Publicados</option>
            <option value="draft" @selected(request('status') === 'draft')>Borradores</option>
        </select>
        <button class="btn-secondary">Filtrar</button>
    </form>

    @if ($posts->isEmpty())
        <div class="rounded-2xl border-2 border-dashed border-sage bg-white p-10 text-center">
            <p class="mb-4 text-lg">No hay posts que coincidan.</p>
            <a href="{{ route('admin.posts.create') }}" class="btn-secondary">Escribir un post</a>
        </div>
    @else
        <ul class="divide-y divide-sage overflow-hidden rounded-2xl bg-white">
            @foreach ($posts as $post)
                <li class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center">
                    @if ($post->cover_path)
                        <img src="{{ storage_url($post->cover_path) }}" alt="" class="h-16 w-24 shrink-0 rounded-lg object-cover">
                    @else
                        <div class="h-16 w-24 shrink-0 rounded-lg bg-sage-100" aria-hidden="true"></div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-navy">{{ $post->title }}</p>
                        <p class="text-sm text-muted">
                            @if ($post->status === 'published')
                                {{ $post->published_at?->isFuture() ? 'Programado para' : 'Publicado el' }} {{ $post->published_at?->translatedFormat('j \d\e F \d\e Y, H:i') }}
                            @else
                                Borrador · editado {{ $post->updated_at->diffForHumans() }}
                            @endif
                        </p>
                    </div>
                    <span @class(['shrink-0 rounded-full px-3 py-1 text-sm font-semibold',
                        'bg-sage-100 text-leaf-700' => $post->status === 'published',
                        'bg-gold/20 text-navy' => $post->status === 'draft'])>{{ $post->status === 'published' ? 'Publicado' : 'Borrador' }}</span>
                    <div class="flex shrink-0 gap-2">
                        @if ($post->status === 'published' && Route::has('posts.show'))
                            <a href="{{ route('posts.show', $post) }}" target="_blank" class="btn min-h-11 px-3 py-2 text-leaf-700 underline">Ver</a>
                        @endif
                        <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn-secondary min-h-11 px-4 py-2">Editar</a>
                        <form method="POST" action="{{ route('admin.posts.destroy', $post->id) }}" onsubmit="return confirm('¿Seguro que quieres borrar este post? No se puede deshacer.')">
                            @csrf
                            @method('DELETE')
                            <button class="btn min-h-11 border-2 border-red-700 px-4 py-2 text-red-700 hover:bg-red-700 hover:text-white">Borrar</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
        <div class="mt-6">{{ $posts->links() }}</div>
    @endif
@endsection
