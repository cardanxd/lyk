@extends('layouts.admin')

@section('title', 'Ajustes generales')

@section('content')
    @php($s = $settings)
    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <nav aria-label="Secciones de ajustes" class="flex flex-wrap gap-2">
            @foreach (['identidad' => 'Identidad', 'nosotros' => 'Nosotros', 'contacto' => 'Contacto', 'redes' => 'Redes sociales', 'aviso' => 'Aviso y SEO'] as $id => $label)
                <a href="#{{ $id }}" class="rounded-full bg-white px-4 py-2 font-semibold text-navy hover:bg-sage-100">{{ $label }}</a>
            @endforeach
        </nav>

        <section id="identidad" class="space-y-5 rounded-2xl bg-white p-6">
            <h2 class="text-xl">Identidad</h2>
            <x-admin.image-field name="logo" label="Logo" :current-url="$s->imageUrl('logo_path', 'images/brand/logo.png')" />
            <x-admin.field name="site_name" label="Nombre del sitio" :value="$s->get('site_name')" required />
            <x-admin.field name="tagline" label="Lema" :value="$s->get('tagline')" required help="Aparece en la portada y en el pie de página." />
        </section>

        <section id="nosotros" class="space-y-5 rounded-2xl bg-white p-6">
            <h2 class="text-xl">Nosotros</h2>
            <x-admin.image-field name="about_image" label="Foto de nosotros" :current-url="$s->get('about_image_path') ? $s->imageUrl('about_image_path', 'images/brand/portada.jpg') : null" />
            <x-admin.field name="history" label="Historia" :value="$s->get('history')" rows="5" />
            <x-admin.field name="mission" label="Misión" :value="$s->get('mission')" rows="4" />
            <x-admin.field name="vision" label="Visión" :value="$s->get('vision')" rows="4" />
            <x-admin.field name="values" label="Valores" :value="$s->get('values')" rows="5" help="Escribe un valor por línea." />
        </section>

        <section id="contacto" class="space-y-5 rounded-2xl bg-white p-6">
            <h2 class="text-xl">Contacto</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-admin.field name="phone" label="Teléfono" :value="$s->get('phone')" type="tel" required />
                <x-admin.field name="whatsapp" label="WhatsApp" :value="$s->get('whatsapp')" type="tel" help="Con lada de país, ej. +525512345678. Si lo dejas vacío no se muestra el botón." />
                <x-admin.field name="email" label="Correo de contacto" :value="$s->get('email')" type="email" required />
                <x-admin.field name="notify_email" label="Correo para avisos de mensajes" :value="$s->get('notify_email')" type="email" help="Si lo dejas vacío se usa el correo de contacto." />
            </div>
            <x-admin.field name="address" label="Dirección" :value="$s->get('address')" />
            <x-admin.field name="schedule" label="Horario" :value="$s->get('schedule')" />
            <x-admin.field name="map_embed_url" label="URL del mapa de Google" :value="$s->get('map_embed_url')" type="url" help="En Google Maps: Compartir → Insertar un mapa → copia sólo la dirección que está dentro de src=&quot;…&quot;." />
        </section>

        <section id="redes" class="space-y-5 rounded-2xl bg-white p-6">
            <h2 class="text-xl">Redes sociales</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-admin.field name="facebook_url" label="Facebook" :value="$s->get('facebook_url')" type="url" placeholder="https://facebook.com/…" />
                <x-admin.field name="instagram_url" label="Instagram" :value="$s->get('instagram_url')" type="url" placeholder="https://instagram.com/…" />
                <x-admin.field name="tiktok_url" label="TikTok" :value="$s->get('tiktok_url')" type="url" placeholder="https://tiktok.com/@…" />
                <x-admin.field name="youtube_url" label="YouTube" :value="$s->get('youtube_url')" type="url" placeholder="https://youtube.com/@…" />
            </div>
        </section>

        <section id="aviso" class="space-y-5 rounded-2xl bg-white p-6">
            <h2 class="text-xl">Aviso de privacidad y buscadores</h2>
            <x-admin.field name="privacy_notice" label="Aviso de privacidad" :value="$s->get('privacy_notice')" rows="12" />
            <x-admin.field name="seo_description" label="Descripción para Google y redes" :value="$s->get('seo_description')" rows="2" maxlength="300" help="Una o dos frases. Aparece en los resultados de Google y al compartir el sitio." />
        </section>

        <div class="sticky bottom-0 -mx-4 border-t border-sage bg-cream/95 px-4 py-4 sm:mx-0 sm:rounded-2xl">
            <button class="btn-primary">Guardar ajustes</button>
        </div>
    </form>
@endsection
