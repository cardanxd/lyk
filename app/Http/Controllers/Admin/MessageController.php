<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\MessageTag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $view = array_key_exists($request->query('vista'), ContactMessage::VIEWS) ? $request->query('vista') : 'todos';
        $tagId = $request->integer('etiqueta') ?: null;

        $base = fn () => ContactMessage::query()
            ->when($tagId, fn ($q) => $q->whereHas('tags', fn ($t) => $t->whereKey($tagId)));

        $counts = collect(ContactMessage::VIEWS)
            ->mapWithKeys(fn ($label, $slug) => [$slug => $base()->inView($slug)->count()])
            ->all();

        return view('admin.messages.index', [
            'messages' => $base()->inView($view)->with('tags')->latest()->paginate(25)->withQueryString(),
            'counts' => $counts,
            'view' => $view,
            'tags' => MessageTag::withCount('messages')->orderBy('name')->get(),
            'tagId' => $tagId,
        ]);
    }

    public function show(ContactMessage $message)
    {
        if (! $message->read_at) {
            $message->update(['read_at' => now()]);
        }

        return view('admin.messages.show', [
            'message' => $message->load('tags'),
            'tags' => MessageTag::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, ContactMessage $message)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(ContactMessage::STATUSES))],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:message_tags,id'],
        ], [], ['status' => 'estado', 'tags.*' => 'etiqueta']);

        $message->update(['status' => $data['status']]);
        $message->tags()->sync($data['tags'] ?? []);

        return back()->with('status', 'Mensaje actualizado.');
    }

    public function toggleRead(ContactMessage $message)
    {
        $message->update(['read_at' => $message->read_at ? null : now()]);

        return $message->read_at
            ? back()->with('status', 'Marcado como leído.')
            : redirect()->route('admin.messages.index')->with('status', 'Marcado como no leído.');
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();

        return redirect()->route('admin.messages.index')->with('status', 'Mensaje eliminado.');
    }
}
