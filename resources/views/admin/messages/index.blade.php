@extends('layouts.admin')

@section('title', 'Mensajes')

@section('content')
    <p class="mb-5 text-lg">{{ $unread }} sin leer</p>

    @if ($messages->isEmpty())
        <div class="rounded-2xl border-2 border-dashed border-sage bg-white p-10 text-center text-lg">Aún no has recibido mensajes desde el formulario de contacto.</div>
    @else
        <ul class="divide-y divide-sage overflow-hidden rounded-2xl bg-white">
            @foreach ($messages as $message)
                <li>
                    <a href="{{ route('admin.messages.show', $message) }}" class="flex flex-col gap-1 p-4 hover:bg-sage-100/50 sm:flex-row sm:items-center sm:gap-4">
                        <span @class(['h-3 w-3 shrink-0 rounded-full', 'bg-gold' => ! $message->read_at, 'bg-transparent' => $message->read_at]) aria-hidden="true"></span>
                        <span class="min-w-0 flex-1">
                            <span @class(['block text-navy', 'font-bold' => ! $message->read_at, 'font-semibold' => $message->read_at])>
                                {{ $message->name }} @if ($message->topic)<span class="font-normal text-muted">· {{ $message->topic }}</span>@endif
                                @unless ($message->read_at)<span class="sr-only">(sin leer)</span>@endunless
                            </span>
                            <span class="block truncate text-muted">{{ $message->message }}</span>
                        </span>
                        <span class="shrink-0 text-sm text-muted">{{ $message->created_at->diffForHumans() }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="mt-6">{{ $messages->links() }}</div>
    @endif
@endsection
