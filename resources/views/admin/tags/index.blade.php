@extends('layouts.admin')

@section('title', 'Etiquetas de mensajes')

@section('actions')
    <a href="{{ route('admin.tags.create') }}" class="btn-primary">+ Nueva etiqueta</a>
@endsection

@section('content')
    <p class="mb-5 text-lg">Usa etiquetas para agrupar tus mensajes, por ejemplo por tema (“Modalidad 40”), prioridad (“Urgente”) o sucursal.</p>

    @if ($tags->isEmpty())
        <div class="rounded-2xl border-2 border-dashed border-sage bg-white p-10 text-center">
            <p class="mb-4 text-lg">Todavía no hay etiquetas.</p>
            <a href="{{ route('admin.tags.create') }}" class="btn-secondary">Crear la primera</a>
        </div>
    @else
        <ul class="divide-y divide-sage overflow-hidden rounded-2xl bg-white">
            @foreach ($tags as $tag)
                <li class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center">
                    <div class="flex-1"><x-admin.tag-badge :tag="$tag" class="text-base" /></div>
                    <a href="{{ route('admin.messages.index', ['etiqueta' => $tag->id]) }}" class="text-leaf-700 underline">{{ $tag->messages_count }} {{ $tag->messages_count === 1 ? 'mensaje' : 'mensajes' }}</a>
                    <div class="flex shrink-0 gap-2">
                        <a href="{{ route('admin.tags.edit', $tag) }}" class="btn-secondary min-h-11 px-4 py-2">Editar</a>
                        <form method="POST" action="{{ route('admin.tags.destroy', $tag) }}" onsubmit="return confirm('¿Borrar la etiqueta «{{ $tag->name }}»? Los mensajes no se borran, sólo pierden esta etiqueta.')">
                            @csrf
                            @method('DELETE')
                            <button class="btn min-h-11 border-2 border-red-700 px-4 py-2 text-red-700 hover:bg-red-700 hover:text-white">Borrar</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
