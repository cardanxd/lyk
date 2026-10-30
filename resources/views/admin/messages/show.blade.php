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
        <form method="POST" action="{{ route('admin.messages.update', $message) }}" class="space-y-5 rounded-xl border border-sage p-5">
            @csrf
            @method('PATCH')
            <div>
                <label for="status" class="form-label">Estado</label>
                <select id="status" name="status" class="form-input sm:max-w-xs">
                    @foreach (\App\Models\ContactMessage::STATUSES as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $message->status) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <fieldset>
                <legend class="form-label">Etiquetas</legend>
                @if ($tags->isEmpty())
                    <p class="text-muted">Aún no tienes etiquetas. <a href="{{ route('admin.tags.create') }}" class="font-semibold text-leaf-700 underline">Crear etiqueta</a></p>
                @else
                    <div class="flex flex-wrap gap-2">
                        @foreach ($tags as $tag)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="peer sr-only" @checked(in_array($tag->id, old('tags', $message->tags->pluck('id')->all())))>
                                <span class="inline-flex min-h-11 items-center gap-2 rounded-full border-2 border-transparent px-1 opacity-60 peer-checked:border-navy peer-checked:opacity-100 peer-focus-visible:ring-2 peer-focus-visible:ring-gold">
                                    <x-admin.tag-badge :tag="$tag" />
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <p class="form-help">Toca una etiqueta para ponerla o quitarla. <a href="{{ route('admin.tags.create') }}" class="text-leaf-700 underline">Crear etiqueta</a></p>
                @endif
                @foreach ($errors->get('tags.*') as $messages)<p class="form-error">{{ $messages[0] }}</p>@endforeach
            </fieldset>
            <button class="btn-primary">Guardar</button>
        </form>

        <div class="flex flex-wrap gap-3">
            @php($wa = preg_replace('/\D/', '', $message->phone))
            <a href="https://wa.me/{{ strlen($wa) === 10 ? '52'.$wa : $wa }}" target="_blank" rel="noopener" class="btn-primary">Responder por WhatsApp</a>
            <form method="POST" action="{{ route('admin.messages.read', $message) }}">
                @csrf
                @method('PATCH')
                <button class="btn-secondary">Marcar como no leído</button>
            </form>
            <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" onsubmit="return confirm('¿Borrar este mensaje? No se puede deshacer.')">
                @csrf
                @method('DELETE')
                <button class="btn border-2 border-red-700 text-red-700 hover:bg-red-700 hover:text-white">Borrar</button>
            </form>
        </div>
    </article>
@endsection
