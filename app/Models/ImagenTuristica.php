<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagenTuristica extends Model
{
    use HasFactory;

    protected $table = 'imagenes_turisticas';

    protected $fillable = [
        'descripcion', // Nombre de la columna en la DB
        'municipio_id',
        'ruta_imagen', // Nombre de la columna en la DB
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }
}
