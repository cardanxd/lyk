<!DOCTYPE html>
<html lang="es">
<body style="margin:0;padding:24px;background:#F7F5EF;font-family:Arial,sans-serif;color:#1F2933;">
    <div style="max-width:560px;margin:0 auto;background:#ffffff;border-radius:12px;padding:24px;">
        <h1 style="margin:0 0 16px;font-size:20px;color:#1B2A4A;">Nuevo mensaje de contacto</h1>
        <table style="font-size:16px;line-height:1.5;">
            <tr><td style="padding-right:12px;font-weight:bold;">Nombre</td><td>{{ $contactMessage->name }}</td></tr>
            <tr><td style="padding-right:12px;font-weight:bold;">Teléfono</td><td>{{ $contactMessage->phone }}</td></tr>
            @if ($contactMessage->email)<tr><td style="padding-right:12px;font-weight:bold;">Correo</td><td>{{ $contactMessage->email }}</td></tr>@endif
            @if ($contactMessage->topic)<tr><td style="padding-right:12px;font-weight:bold;">Tema</td><td>{{ $contactMessage->topic }}</td></tr>@endif
        </table>
        <p style="margin:16px 0;padding:12px;background:#F7F5EF;border-radius:8px;font-size:16px;white-space:pre-wrap;">{{ $contactMessage->message }}</p>
        <p><a href="{{ route('admin.messages.show', $contactMessage->id) }}" style="display:inline-block;padding:12px 20px;background:#E3A92B;color:#1B2A4A;border-radius:999px;font-weight:bold;text-decoration:none;">Ver en el panel</a></p>
    </div>
</body>
</html>
