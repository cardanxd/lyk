@extends('admin.orderable.form')

@section('fields')
    <x-admin.field name="name" label="Nombre" :value="$item->name" required />
    <x-admin.field name="location" label="Ciudad" :value="$item->location" />
    <x-admin.field name="quote" label="Testimonio" :value="$item->quote" rows="5" required />
    <x-admin.image-field name="photo" label="Foto (opcional)"
        :current-url="$item->photo_path ? Storage::disk('public')->url($item->photo_path) : null" />
@endsection
