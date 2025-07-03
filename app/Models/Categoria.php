<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Para generar el slug

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'slug',
    ];

    /**
     * Define la relación "hasMany" con el modelo Noticia.
     * Una categoría puede tener muchas noticias.
     */
    public function noticias()
    {
        return $this->hasMany(Noticia::class);
    }

    /**
     * Método "boot" para generar automáticamente el slug antes de guardar.
     */
    protected static function boot()
    {
        parent::boot();

        // Al crear una nueva categoría, genera su slug
        static::creating(function ($categoria) {
            $categoria->slug = Str::slug($categoria->nombre);
        });

        // Al actualizar una categoría, si el nombre cambia, actualiza también el slug
        static::updating(function ($categoria) {
            if ($categoria->isDirty('nombre')) {
                $categoria->slug = Str::slug($categoria->nombre);
            }
        });
    }
}
