<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagenTuristica extends Model
{
    use HasFactory;

    protected $fillable = [
        'municipio_id', 'ruta_imagen'
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }
}
