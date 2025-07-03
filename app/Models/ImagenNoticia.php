<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagenNoticia extends Model
{
    use HasFactory;

    protected $table = 'imagenes_noticias'; // Nombre explícito de la tabla

    protected $fillable = [
        'noticia_id',
        'ruta_imagen',
        'descripcion',
    ];

    /**
     * Define la relación "belongsTo" con el modelo Noticia.
     * Una imagen pertenece a una noticia.
     */
    public function noticia()
    {
        return $this->belongsTo(Noticia::class);
    }
}
