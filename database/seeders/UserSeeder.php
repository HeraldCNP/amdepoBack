<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory;

class UserSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create(); // Crea una instancia de Faker

        // Usuario inicial
        $user = User::create([
            'name' => 'Herald',
            'email' => 'heraldcnp@gmail.com',
            'password' => Hash::make('12345678'),
        ]);

        $user->profile()->create([
            'ci' => '6680287',
            'lastName' => 'Choque Vargas',
            'phone' => '72367995',
            'address' => 'h vasquez 186',
        ]);

        $user = User::create([
            'name' => 'Cinthia',
            'email' => 'Potosiamdepo01@gmail.com',
            'password' => Hash::make('Amdepo2024Bolivi@'),
        ]);

        $user->profile()->create([
            'ci' => '8631065',
            'lastName' => 'Arenas Vela',
            'phone' => '63689078',
            'address' => '',
        ]);



        // Crear 49 usuarios adicionales
        // User::factory(49)->create()->each(function ($user) use ($faker) { // Pasa $faker al closure
        //     $user->profile()->create([
        //         'ci' => $faker->numerify('########'),
        //         'lastName' => $faker->lastName,
        //         'phone' => $faker->numerify('7########'),
        //         'address' => $faker->address,
        //     ]);
        // });
    }
}
