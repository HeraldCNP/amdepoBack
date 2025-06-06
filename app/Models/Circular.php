<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Circular extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'imagenCircular',
        'user_id',
    ];

    /**
     * Get the user that owns the circular.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
