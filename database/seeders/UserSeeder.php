<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user = User::create([
            'name' => 'Herald',
            'email' => 'heraldcnp@gmail.com',
            'password' => Hash::make('12345678'), // Encriptar la contraseña
        ]);

        $user->profile()->create([
            'ci' => '6680287',
            'lastName' => 'Choque Vargas',
            'phone' => '72367995',
            'address' => 'h vasquez 186',
        ]);
    }
}
