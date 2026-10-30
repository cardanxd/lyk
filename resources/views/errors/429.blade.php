@extends('layouts.public')

@section('title', 'Espera un momento')

@section('content')
    <section class="container-page max-w-2xl py-20 text-center">
        <h1 class="mb-4 text-3xl">Demasiados intentos</h1>
        <p class="mb-8 text-lg text-muted">Recibimos varios envíos seguidos. Espera un minuto e inténtalo de nuevo, o escríbenos directamente.</p>
        <a href="{{ route('home') }}" class="btn-primary">Volver al inicio</a>
    </section>
@endsection
