<x-mail::message>
# Nuevo mensaje de contacto

**Nombre:** {{ $contactMessage->name }}
**Teléfono:** {{ $contactMessage->phone }}
@if ($contactMessage->email)
**Correo:** {{ $contactMessage->email }}
@endif
@if ($contactMessage->topic)
**Tema:** {{ $contactMessage->topic }}
@endif

{{ $contactMessage->message }}

<x-mail::button :url="route('admin.messages.show', $contactMessage->id)">
Ver en el panel
</x-mail::button>
</x-mail::message>
