<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'permissions' => $this->permissions->map(function ($permission) {
                return [
                    'name' => $permission->name,
                    'description' => $permission->description,
                ];
            })->toArray(), // Convierte la colección a un array plano
        ];
    }
}
