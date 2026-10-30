@extends('layouts.admin')

@section('title', $labels[0])

@section('actions')
    <a href="{{ route("admin.$name.create") }}" class="btn-primary">+ Agregar {{ Str::lower($labels[1]) }}</a>
@endsection

@section('content')
    @if ($items->isEmpty())
        <div class="rounded-2xl border-2 border-dashed border-sage bg-white p-10 text-center">
            <p class="mb-4 text-lg">Todavía no hay elementos.</p>
            <a href="{{ route("admin.$name.create") }}" class="btn-secondary">Agregar el primero</a>
        </div>
    @else
        <ul class="divide-y divide-sage overflow-hidden rounded-2xl bg-white">
            @foreach ($items as $item)
                <li class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center">
                    <span class="w-14 shrink-0 text-sm text-muted">Orden {{ $item->sort_order }}</span>
                    <div class="min-w-0 flex-1">
                        @include("admin.$name.row", ['item' => $item])
                    </div>
                    <span @class(['shrink-0 rounded-full px-3 py-1 text-sm font-semibold',
                        'bg-sage-100 text-leaf-700' => $item->is_active,
                        'bg-gray-100 text-muted' => ! $item->is_active])>{{ $item->is_active ? 'Visible' : 'Oculto' }}</span>
                    <div class="flex shrink-0 gap-2">
                        <a href="{{ route("admin.$name.edit", $item->id) }}" class="btn-secondary min-h-11 px-4 py-2">Editar</a>
                        <form method="POST" action="{{ route("admin.$name.destroy", $item->id) }}" onsubmit="return confirm('¿Seguro que quieres borrarlo? No se puede deshacer.')">
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
