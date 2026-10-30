<?php

namespace App\Http\Controllers;

use App\Mail\NewContactMessage;
use App\Models\Branch;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Throwable;

class ContactController extends Controller
{
    private const SUCCESS = '¡Gracias! Recibimos tu mensaje y te contactaremos muy pronto.';

    public function create()
    {
        return view('pages.contact', ['topics' => ContactMessage::TOPICS, 'branches' => Branch::active()->get()]);
    }

    public function store(Request $request)
    {
        // Campo trampa invisible: si un bot lo llena, fingimos éxito sin guardar.
        if ($request->filled('website')) {
            return redirect()->route('contact')->with('status', self::SUCCESS);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'regex:/^[\d\s+()\-]{10,20}$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'topic' => ['nullable', Rule::in(ContactMessage::TOPICS)],
            'message' => ['required', 'string', 'max:3000'],
            'privacy' => ['accepted'],
        ], [
            'phone.regex' => 'Escribe un teléfono de 10 dígitos.',
            'privacy.accepted' => 'Debes aceptar el aviso de privacidad para enviar tu mensaje.',
        ], [
            'name' => 'nombre', 'phone' => 'teléfono', 'email' => 'correo', 'topic' => 'tema', 'message' => 'mensaje',
        ]);

        $message = ContactMessage::create($data);

        $this->notifyOwner($message);

        return redirect()->route('contact')->with('status', self::SUCCESS);
    }

    private function notifyOwner(ContactMessage $message): void
    {
        $to = setting('notify_email') ?: setting('email');

        if (! $to) {
            return;
        }

        try {
            Mail::to($to)->send(new NewContactMessage($message));
        } catch (Throwable $e) {
            report($e); // el mensaje ya está guardado en la bandeja
        }
    }
}
