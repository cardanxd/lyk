{{-- Envoltura común: $item, $labels, $name; los campos propios van en el slot "fields". --}}
@extends('layouts.admin')

@section('title', ($item->exists ? 'Editar ' : 'Nueva ').Str::lower($labels[1]))

@section('actions')
    <a href="{{ route("admin.$name.index") }}" class="text-leaf-700 underline">← Volver a {{ Str::lower($labels[0]) }}</a>
@endsection

@section('content')
    <form method="POST" enctype="multipart/form-data"
          action="{{ $item->exists ? route("admin.$name.update", $item->id) : route("admin.$name.store") }}"
          class="space-y-5 rounded-2xl bg-white p-6">
        @csrf
        @if ($item->exists) @method('PUT') @endif

        @yield('fields')

        <div class="grid gap-5 sm:grid-cols-2">
            <x-admin.field name="sort_order" label="Orden" type="number" min="0" :value="$item->sort_order ?? 0" help="Los números menores aparecen primero." />
            <label class="flex items-center gap-3 self-center">
                <input type="checkbox" name="is_active" value="1" class="h-6 w-6 rounded accent-leaf" @checked(old('is_active', $item->exists ? $item->is_active : true))>
                <span class="font-semibold text-navy">Visible en el sitio</span>
            </label>
        </div>

        <button class="btn-primary">Guardar</button>
    </form>
@endsection
