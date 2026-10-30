@extends('layouts.public')

@section('title', 'Posts')

@section('content')
    @include('partials.page-header', [
        'eyebrow' => 'Guías y noticias',
        'title' => 'Información clara sobre tu pensión',
        'lead' => 'Modalidad 40, AFORE, Ley 73 y Ley 97: te explicamos los cambios y lo que significan para ti.',
    ])

    <section class="container-page py-12 md:py-16">
        @if ($posts->isEmpty())
            <p class="rounded-2xl bg-white p-10 text-center text-lg text-muted">Muy pronto publicaremos nuestras primeras guías.</p>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>
            <div class="mt-10">{{ $posts->links() }}</div>
        @endif
    </section>
@endsection
