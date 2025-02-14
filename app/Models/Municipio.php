<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected $fillable = [
        'nombre',
        'descripcion',
        'direccion',
        'telefono',
        'email',
        'sitio_web',
        'latitud',
        'longitud',
        'poblacion',
        'superficie',
        'historia',
        'gentilicio',
        'alcalde_nombre',
        'alcalde_foto',
        'alcalde_descripcion',
        'slug',
        'user_id',
    ];

    public function concejales()
    {
        return $this->hasMany(Concejal::class);
    }

    public function imagenesTuristicas()
    {
        return $this->hasMany(ImagenTuristica::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
