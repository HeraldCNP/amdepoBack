<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at->toDateTimeString(),
            'profile' => $this->whenLoaded('profile', function () { // Carga condicional
                return [
                    'lastName' => $this->profile->lastName,
                    'ci' => $this->profile->ci,
                    'phone' => $this->profile->phone,
                    'address' => $this->profile->address,
                    // ... otros campos de UserProfile
                ];
            }),
            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                    ];
                })->toArray();
            }),
        ];
    }
}
