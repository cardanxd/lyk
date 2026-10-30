<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MessageTag extends Model
{
    use HasFactory;

    /** Colores disponibles => clases de la etiqueta (contraste AA). */
    public const COLORS = [
        'gray' => 'bg-gray-100 text-gray-800 border-gray-300',
        'green' => 'bg-sage-100 text-leaf-700 border-leaf/40',
        'blue' => 'bg-blue-50 text-blue-800 border-blue-200',
        'gold' => 'bg-gold/20 text-navy border-gold/50',
        'red' => 'bg-red-50 text-red-800 border-red-200',
        'purple' => 'bg-purple-50 text-purple-800 border-purple-200',
    ];

    public const COLOR_NAMES = [
        'gray' => 'Gris', 'green' => 'Verde', 'blue' => 'Azul', 'gold' => 'Dorado', 'red' => 'Rojo', 'purple' => 'Morado',
    ];

    protected $fillable = ['name', 'color'];

    public function messages(): BelongsToMany
    {
        return $this->belongsToMany(ContactMessage::class);
    }

    public function classes(): string
    {
        return self::COLORS[$this->color] ?? self::COLORS['gray'];
    }
}
