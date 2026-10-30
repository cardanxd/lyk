@extends('layouts.admin')

@section('title', $tag->exists ? 'Editar etiqueta' : 'Nueva etiqueta')

@section('actions')
    <a href="{{ route('admin.tags.index') }}" class="text-leaf-700 underline">← Volver a etiquetas</a>
@endsection

@section('content')
    <form method="POST" action="{{ $tag->exists ? route('admin.tags.update', $tag) : route('admin.tags.store') }}" class="max-w-lg space-y-5 rounded-2xl bg-white p-6">
        @csrf
        @if ($tag->exists) @method('PUT') @endif
        <x-admin.field name="name" label="Nombre" :value="$tag->name" required maxlength="40" placeholder="Ej. Urgente" />
        <fieldset>
            <legend class="form-label">Color</legend>
            <div class="flex flex-wrap gap-2">
                @foreach (\App\Models\MessageTag::COLORS as $color => $classes)
                    <label class="cursor-pointer">
                        <input type="radio" name="color" value="{{ $color }}" class="peer sr-only" @checked(old('color', $tag->color) === $color)>
                        <span class="inline-flex min-h-11 items-center rounded-full border-2 px-4 font-semibold {{ $classes }} peer-checked:ring-2 peer-checked:ring-navy peer-checked:ring-offset-2 peer-focus-visible:ring-2 peer-focus-visible:ring-gold">{{ \App\Models\MessageTag::COLOR_NAMES[$color] }}</span>
                    </label>
                @endforeach
            </div>
            @error('color')<p class="form-error">{{ $message }}</p>@enderror
        </fieldset>
        <button class="btn-primary">Guardar etiqueta</button>
    </form>
@endsection
