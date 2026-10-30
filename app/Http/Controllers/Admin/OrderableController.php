<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ImageUploader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * CRUD compartido para contenido ordenable (portadas, servicios, preguntas, testimonios).
 * Cada subclase define su modelo, nombres y reglas; la imagen es opcional.
 */
abstract class OrderableController extends Controller
{
    /** @return class-string<Model> */
    abstract protected function model(): string;

    /** Prefijo de rutas y carpeta de vistas, ej. "slides". */
    abstract protected function name(): string;

    /** Títulos para las vistas: [plural, singular]. */
    abstract protected function labels(): array;

    abstract protected function rules(?Model $item): array;

    /** Campo de archivo => columna, ej. ['image' => 'image_path']. */
    protected function imageField(): ?array
    {
        return null;
    }

    protected function imageRequiredOnCreate(): bool
    {
        return false;
    }

    public function index()
    {
        return view('admin.orderable.index', [
            'items' => $this->model()::ordered()->get(),
            'labels' => $this->labels(),
            'name' => $this->name(),
        ]);
    }

    public function create()
    {
        return $this->form(new ($this->model()));
    }

    public function store(Request $request)
    {
        $item = new ($this->model());
        $this->save($request, $item);

        return redirect()->route("admin.{$this->name()}.index")->with('status', "{$this->labels()[1]} creado.");
    }

    public function edit(string $id)
    {
        return $this->form($this->model()::findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $this->save($request, $this->model()::findOrFail($id));

        return redirect()->route("admin.{$this->name()}.index")->with('status', 'Cambios guardados.');
    }

    public function destroy(string $id)
    {
        $item = $this->model()::findOrFail($id);

        if ($field = $this->imageField()) {
            ImageUploader::delete($item->{reset($field)});
        }

        $item->delete();

        return redirect()->route("admin.{$this->name()}.index")->with('status', 'Elemento eliminado.');
    }

    private function form(Model $item)
    {
        return view("admin.{$this->name()}.form", [
            'item' => $item,
            'labels' => $this->labels(),
            'name' => $this->name(),
        ]);
    }

    private function save(Request $request, Model $item): void
    {
        $rules = $this->rules($item->exists ? $item : null) + [
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ];

        if ($field = $this->imageField()) {
            $required = ! $item->exists && $this->imageRequiredOnCreate() ? 'required' : 'nullable';
            $rules[key($field)] = [$required, ...ImageUploader::RULES];
        }

        $data = $request->validate($rules, ImageUploader::messages('image', 'photo'), $this->attributeNames());

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = $request->boolean('is_active');

        if ($field = $this->imageField()) {
            [$input, $column] = [key($field), reset($field)];
            unset($data[$input]);

            if ($request->hasFile($input)) {
                $data[$column] = ImageUploader::replace($item->{$column}, $request->file($input), $this->name());
            }
        }

        $item->fill($data)->save();
    }

    protected function attributeNames(): array
    {
        return [
            'image' => 'imagen', 'photo' => 'foto', 'title' => 'título', 'subtitle' => 'subtítulo',
            'button_text' => 'texto del botón', 'button_url' => 'enlace del botón', 'description' => 'descripción',
            'icon' => 'icono', 'question' => 'pregunta', 'answer' => 'respuesta', 'name' => 'nombre',
            'quote' => 'testimonio', 'location' => 'ciudad', 'sort_order' => 'orden',
        ];
    }
}
