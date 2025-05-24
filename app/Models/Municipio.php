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
        'alcalde_foto',
        'alcalde_nombre',
        'aniversario',
        'circuscripcion',
        'comunidades',
        'descripcion',
        'direccion',
        'email',
        'facebook',
        'ferias',
        'fiestaPatronal',
        'gentilicio',
        'historia',
        'latitud',
        'longitud',
        'nombre',
        'poblacion',
        'provincia',
        'sitio_web',
        'slug',
        'superficie',
        'telefono',
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
