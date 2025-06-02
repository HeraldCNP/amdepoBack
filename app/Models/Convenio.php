<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Convenio extends Model
{
    use HasFactory;

    // Nombre explícito de la tabla si no sigue la convención (Convenio -> convenios)
    // protected $table = 'convenios';

    protected $fillable = [
        'titulo',
        'archivoPdf', // Nombre de la columna en la DB
        'user_id',
    ];

    /**
     * Get the user that owns the convenio.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
