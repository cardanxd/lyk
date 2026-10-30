@extends('layouts.admin')

@section('title', 'Mensajes')

@section('actions')
    <a href="{{ route('admin.tags.index') }}" class="btn-secondary">Administrar etiquetas</a>
@endsection

@section('content')
    @php($statusClasses = ['pending' => 'bg-gold/20 text-navy', 'in_progress' => 'bg-blue-50 text-blue-800', 'done' => 'bg-sage-100 text-leaf-700'])

    <nav aria-label="Agrupar mensajes" class="mb-4 flex gap-2 overflow-x-auto pb-1">
        @foreach (\App\Models\ContactMessage::VIEWS as $slug => $label)
            <a href="{{ route('admin.messages.index', array_filter(['vista' => $slug === 'todos' ? null : $slug, 'etiqueta' => $tagId])) }}"
               @if ($view === $slug) aria-current="page" @endif
               @class([
                   'inline-flex min-h-11 shrink-0 items-center gap-2 rounded-full px-4 font-semibold transition-colors duration-200',
                   'bg-navy text-white' => $view === $slug,
                   'bg-white text-navy hover:bg-sage-100' => $view !== $slug,
               ])>
                {{ $label }}
                <span @class(['rounded-full px-2 text-sm', 'bg-white/20' => $view === $slug, 'bg-sage-100' => $view !== $slug])>{{ $counts[$slug] }}</span>
            </a>
        @endforeach
    </nav>

    @if ($tags->isNotEmpty())
        <div class="mb-5 flex flex-wrap items-center gap-2">
            <span class="text-sm font-semibold text-muted">Etiquetas:</span>
            @if ($tagId)
                <a href="{{ route('admin.messages.index', array_filter(['vista' => $view === 'todos' ? null : $view])) }}" class="inline-flex min-h-9 items-center rounded-full border border-navy px-3 text-sm font-semibold text-navy hover:bg-navy hover:text-white">✕ Quitar filtro</a>
            @endif
            @foreach ($tags as $tag)
                <a href="{{ route('admin.messages.index', array_filter(['vista' => $view === 'todos' ? null : $view, 'etiqueta' => $tag->id])) }}"
                   @if ($tagId === $tag->id) aria-current="true" @endif
                   @class(['rounded-full transition-shadow duration-200 hover:shadow', 'ring-2 ring-navy ring-offset-2' => $tagId === $tag->id])>
                    <x-admin.tag-badge :tag="$tag" class="min-h-9">{{ $tag->name }}</x-admin.tag-badge>
                </a>
            @endforeach
        </div>
    @else
        <p class="mb-5 text-muted">Aún no tienes etiquetas. <a href="{{ route('admin.tags.create') }}" class="font-semibold text-leaf-700 underline">Crea la primera</a> para agrupar tus mensajes (por ejemplo: “Modalidad 40”, “Urgente”).</p>
    @endif

    @if ($messages->isEmpty())
        <div class="rounded-2xl border-2 border-dashed border-sage bg-white p-10 text-center text-lg">No hay mensajes en esta vista.</div>
    @else
        <ul class="divide-y divide-sage overflow-hidden rounded-2xl bg-white">
            @foreach ($messages as $message)
                <li>
                    <a href="{{ route('admin.messages.show', $message) }}" class="flex flex-col gap-2 p-4 hover:bg-sage-100/50 sm:flex-row sm:items-start sm:gap-4">
                        <span @class(['mt-2 h-3 w-3 shrink-0 rounded-full', 'bg-gold' => ! $message->read_at, 'bg-transparent' => $message->read_at]) aria-hidden="true"></span>
                        <span class="min-w-0 flex-1">
                            <span @class(['block text-navy', 'font-bold' => ! $message->read_at, 'font-semibold' => $message->read_at])>
                                {{ $message->name }} @if ($message->topic)<span class="font-normal text-muted">· {{ $message->topic }}</span>@endif
                                @unless ($message->read_at)<span class="sr-only">(sin leer)</span>@endunless
                            </span>
                            <span class="block truncate text-muted">{{ $message->message }}</span>
                            <span class="mt-2 flex flex-wrap gap-1.5">
                                <span class="rounded-full px-2.5 py-0.5 text-sm font-semibold {{ $statusClasses[$message->status] ?? '' }}">{{ $message->statusLabel() }}</span>
                                @foreach ($message->tags as $tag)
                                    <x-admin.tag-badge :tag="$tag" />
                                @endforeach
                            </span>
                        </span>
                        <span class="shrink-0 text-sm text-muted">{{ $message->created_at->diffForHumans() }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="mt-6">{{ $messages->links() }}</div>
    @endif
@endsection
