<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;

class MessageController extends Controller
{
    public function index()
    {
        return view('admin.messages.index', [
            'messages' => ContactMessage::latest()->paginate(25),
            'unread' => ContactMessage::unread()->count(),
        ]);
    }

    public function show(ContactMessage $message)
    {
        if (! $message->read_at) {
            $message->update(['read_at' => now()]);
        }

        return view('admin.messages.show', ['message' => $message]);
    }

    public function toggle(ContactMessage $message)
    {
        $message->update(['read_at' => $message->read_at ? null : now()]);

        return back()->with('status', $message->read_at ? 'Marcado como atendido.' : 'Marcado como pendiente.');
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();

        return redirect()->route('admin.messages.index')->with('status', 'Mensaje eliminado.');
    }
}
