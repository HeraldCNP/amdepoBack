<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    protected $fillable = [
        'municipio_id', 'titulo', 'descripcion', 'ruta_archivo', 'tipo_archivo', 'gestion', 'user_id'
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }
}
