<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MessageTag;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MessageTagController extends Controller
{
    public function index()
    {
        return view('admin.tags.index', ['tags' => MessageTag::withCount('messages')->orderBy('name')->get()]);
    }

    public function create()
    {
        return view('admin.tags.form', ['tag' => new MessageTag(['color' => 'gray'])]);
    }

    public function store(Request $request)
    {
        MessageTag::create($this->validated($request));

        return redirect()->route('admin.tags.index')->with('status', 'Etiqueta creada.');
    }

    public function edit(MessageTag $tag)
    {
        return view('admin.tags.form', ['tag' => $tag]);
    }

    public function update(Request $request, MessageTag $tag)
    {
        $tag->update($this->validated($request, $tag));

        return redirect()->route('admin.tags.index')->with('status', 'Etiqueta actualizada.');
    }

    public function destroy(MessageTag $tag)
    {
        $tag->delete(); // la tabla pivote borra en cascada; los mensajes se conservan

        return redirect()->route('admin.tags.index')->with('status', 'Etiqueta eliminada.');
    }

    private function validated(Request $request, ?MessageTag $tag = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:40', function (string $attribute, mixed $value, Closure $fail) use ($tag) {
                $exists = MessageTag::whereRaw('LOWER(name) = ?', [mb_strtolower(trim((string) $value))])
                    ->when($tag, fn ($q) => $q->whereKeyNot($tag->id))
                    ->exists();
                if ($exists) {
                    $fail('Ya existe una etiqueta con ese nombre.');
                }
            }],
            'color' => ['required', Rule::in(array_keys(MessageTag::COLORS))],
        ], [], ['name' => 'nombre', 'color' => 'color']);

        $data['name'] = trim($data['name']);

        return $data;
    }
}
