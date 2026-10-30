@extends('layouts.admin')

@section('title', 'Mensaje de '.$message->name)

@section('actions')
    <a href="{{ route('admin.messages.index') }}" class="text-leaf-700 underline">← Volver a mensajes</a>
@endsection

@section('content')
    <article class="space-y-6 rounded-2xl bg-white p-6">
        <dl class="grid gap-4 sm:grid-cols-2">
            <div><dt class="text-sm text-muted">Teléfono</dt><dd class="text-lg font-semibold"><a href="tel:{{ preg_replace('/[^\d+]/', '', $message->phone) }}" class="text-leaf-700 underline">{{ $message->phone }}</a></dd></div>
            <div><dt class="text-sm text-muted">Correo</dt><dd class="text-lg font-semibold">@if ($message->email)<a href="mailto:{{ $message->email }}" class="text-leaf-700 underline">{{ $message->email }}</a>@else — @endif</dd></div>
            <div><dt class="text-sm text-muted">Tema</dt><dd class="text-lg">{{ $message->topic ?: '—' }}</dd></div>
            <div><dt class="text-sm text-muted">Recibido</dt><dd class="text-lg">{{ $message->created_at->translatedFormat('j \d\e F \d\e Y, H:i') }}</dd></div>
        </dl>
        <div>
            <h2 class="mb-2 text-lg">Mensaje</h2>
            <p class="rounded-xl bg-cream p-4 text-lg">{!! nl2br(e($message->message)) !!}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            @php($wa = preg_replace('/\D/', '', $message->phone))
            <a href="https://wa.me/{{ strlen($wa) === 10 ? '52'.$wa : $wa }}" target="_blank" rel="noopener" class="btn-primary">Responder por WhatsApp</a>
            <form method="POST" action="{{ route('admin.messages.toggle', $message) }}">
                @csrf
                @method('PATCH')
                <button class="btn-secondary">{{ $message->read_at ? 'Marcar como pendiente' : 'Marcar como atendido' }}</button>
            </form>
            <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" onsubmit="return confirm('¿Borrar este mensaje? No se puede deshacer.')">
                @csrf
                @method('DELETE')
                <button class="btn border-2 border-red-700 text-red-700 hover:bg-red-700 hover:text-white">Borrar</button>
            </form>
        </div>
    </article>
@endsection
