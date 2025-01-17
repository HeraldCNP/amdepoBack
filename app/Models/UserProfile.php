<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [ // Permite la asignación masiva de estos campos
        'user_id',
        'lastName',
        'ci',
        'phone',
        'address',
        // ... otros campos
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
