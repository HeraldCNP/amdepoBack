<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publicacion extends Model
{
    use HasFactory;

    // Nombre explícito de la tabla si no sigue la convención (Publicacion -> publicacions)
    protected $table = 'publicacions';

    protected $fillable = [
        'titulo',
        'contenido_iframe', // Nombre de la columna en la DB
        'user_id',
    ];

    /**
     * Get the user that owns the publicacion.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
