<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Para generar el slug

class Noticia extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'slug',
        'texto',
        'video_url',
        'categoria_id',
        'user_id',
    ];

    /**
     * Define la relación "belongsTo" con el modelo User.
     * Una noticia pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define la relación "belongsTo" con el modelo Categoria.
     * Una noticia pertenece a una categoría.
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Define la relación "hasMany" con el modelo ImagenNoticia.
     * Una noticia puede tener muchas imágenes.
     */
    public function imagenesNoticias()
    {
        return $this->hasMany(ImagenNoticia::class);
    }

    /**
     * Método "boot" para generar automáticamente el slug antes de guardar.
     */
    protected static function boot()
    {
        parent::boot();

        // Generar el slug automáticamente al crear una noticia
        static::creating(function ($noticia) {
            $noticia->slug = Str::slug($noticia->titulo);
        });

        // Actualizar el slug automáticamente si el título cambia al actualizar
        static::updating(function ($noticia) {
            if ($noticia->isDirty('titulo')) { // isDirty() verifica si el atributo ha cambiado
                $noticia->slug = Str::slug($noticia->titulo);
            }
        });
    }
}
