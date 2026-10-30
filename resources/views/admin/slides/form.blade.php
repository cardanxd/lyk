@extends('admin.orderable.form')

@section('fields')
    <x-admin.image-field name="image" label="Imagen de portada" :required="! $item->exists"
        :current-url="$item->image_path ? storage_url($item->image_path) : null" />
    <p class="form-help -mt-3">Recomendado: horizontal, al menos 1600 × 900 px. Si la imagen ya trae texto, deja vacío el título.</p>
    <x-admin.field name="title" label="Título" :value="$item->title" />
    <x-admin.field name="subtitle" label="Subtítulo" :value="$item->subtitle" />
    <div class="grid gap-5 sm:grid-cols-2">
        <x-admin.field name="button_text" label="Texto del botón" :value="$item->button_text" placeholder="Agenda tu asesoría" />
        <x-admin.field name="button_url" label="Enlace del botón" :value="$item->button_url" placeholder="/contacto" help="Una ruta del sitio (/contacto) o una dirección completa (https://…)." />
    </div>
@endsection
