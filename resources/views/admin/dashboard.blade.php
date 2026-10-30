@extends('layouts.admin')

@section('title', 'Panel')

@section('content')
    <p class="mb-6 text-lg">Hola, {{ auth()->user()->name }}. Desde aquí puedes administrar todo el contenido de tu sitio.</p>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ([
            ['admin.settings.edit', 'Ajustes generales', 'Logo, misión, visión, contacto y redes sociales.'],
            ['admin.branches.index', 'Sucursales', 'Dirección, foto, horario y mapa de cada sucursal.'],
            ['admin.slides.index', 'Portadas', 'Fotos del carrusel de la página de inicio.'],
            ['admin.posts.index', 'Posts', 'Guías, noticias e infografías.'],
            ['admin.services.index', 'Servicios', 'Lo que ofreces a tus clientes.'],
            ['admin.faqs.index', 'Preguntas frecuentes', 'Dudas comunes con su respuesta.'],
            ['admin.testimonials.index', 'Testimonios', 'Opiniones de tus clientes.'],
            ['admin.messages.index', 'Mensajes', 'Solicitudes recibidas desde el formulario de contacto.'],
        ] as [$route, $title, $text])
            @if (Route::has($route))
                <a href="{{ route($route) }}" class="block rounded-2xl border border-sage bg-white p-5 transition-shadow duration-200 hover:shadow-md">
                    <h2 class="mb-1 text-lg">{{ $title }}</h2>
                    <p class="text-muted">{{ $text }}</p>
                </a>
            @endif
        @endforeach
    </div>
@endsection
