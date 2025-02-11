<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Concejal extends Model
{
    use HasFactory;

    protected $fillable = [
        'municipio_id',
        'nombre',
        'foto',
        'descripcion'
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }
}
