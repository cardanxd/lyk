@extends('admin.orderable.form')

@section('fields')
    <x-admin.field name="title" label="Nombre del servicio" :value="$item->title" required />
    <x-admin.field name="description" label="Descripción" :value="$item->description" rows="4" required />
    <fieldset>
        <legend class="form-label">Icono</legend>
        <div class="flex flex-wrap gap-3">
            @foreach (\App\Models\Service::ICONS as $icon)
                <label class="cursor-pointer">
                    <input type="radio" name="icon" value="{{ $icon }}" class="peer sr-only" @checked(old('icon', $item->icon ?? 'document') === $icon)>
                    <span class="flex h-14 w-14 items-center justify-center rounded-xl border-2 border-sage text-navy peer-checked:border-leaf peer-checked:bg-sage-100 peer-focus-visible:ring-2 peer-focus-visible:ring-gold">
                        <x-icon :name="$icon" class="h-7 w-7" /><span class="sr-only">{{ $icon }}</span>
                    </span>
                </label>
            @endforeach
        </div>
        @error('icon')<p class="form-error">{{ $message }}</p>@enderror
    </fieldset>
@endsection
