@extends('layouts.public')

@section('title', 'Aviso de privacidad')

@section('content')
    @include('partials.page-header', ['title' => 'Aviso de privacidad'])

    <section class="container-page max-w-3xl py-12">
        <div class="prose-lyk rounded-2xl bg-white p-6 md:p-10">{!! nl2br(e(setting('privacy_notice', ''))) !!}</div>
    </section>
@endsection
