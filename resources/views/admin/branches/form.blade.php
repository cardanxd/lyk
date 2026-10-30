@extends('admin.orderable.form')

@section('fields')
    <x-admin.field name="name" label="Nombre de la sucursal" :value="$item->name" required placeholder="Sucursal Centro" />
    <x-admin.field name="address" label="Dirección" :value="$item->address" required placeholder="Calle, número, colonia, C.P." />
    <div class="grid gap-5 sm:grid-cols-2">
        <x-admin.field name="city" label="Ciudad y estado" :value="$item->city" placeholder="Puebla, Pue." />
        <x-admin.field name="schedule" label="Horario" :value="$item->schedule" placeholder="Lunes a viernes de 9:00 a 18:00 h" />
        <x-admin.field name="phone" label="Teléfono" type="tel" :value="$item->phone" />
        <x-admin.field name="whatsapp" label="WhatsApp" type="tel" :value="$item->whatsapp" help="Con lada de país, ej. +525512345678." />
        <x-admin.field name="email" label="Correo" type="email" :value="$item->email" />
    </div>
    <x-admin.image-field name="photo" label="Foto de la sucursal" :current-url="storage_url($item->photo_path)" />
    <x-admin.field name="maps_url" label="Enlace de Google Maps (botón “Cómo llegar”)" type="url" :value="$item->maps_url" help="En Google Maps: busca la sucursal → Compartir → Copiar vínculo." />
    <x-admin.field name="map_embed_url" label="URL del mapa insertado (opcional)" type="url" :value="$item->map_embed_url" help="En Google Maps: Compartir → Insertar un mapa → copia sólo la dirección dentro de src=&quot;…&quot;." />
@endsection
