<?php

namespace Database\Seeders;

use App\Models\Municipio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MunicipioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $municipios = [
            [
                'nombre' => 'Betanzos',
                'descripcion' => 'Municipio ubicado en el departamento de Potosí, Bolivia.',
                'direccion' => 'Plaza Principal, Betanzos',
                'telefono' => '+591 12345678',
                'email' => 'info@Betanzos.gob.bo',
                'sitio_web' => 'www.gambetanzos.gob.bo',
                'latitud' => -18.123456,
                'longitud' => -65.789012,
                'poblacion' => 5000,
                'superficie' => 200.50,
                'historia' => 'Betanzos tiene una rica historia...',
                'gentilicio' => 'Betanzeños',
                'alcalde_nombre' => 'Juan Pérez',
                'alcalde_foto' => 'alcalde.jpg',
                'alcalde_descripcion' => 'El alcalde actual de Betanzos...',
                'user_id' => 1, // ID del usuario que crea el municipio (debes tener usuarios en tu tabla users)
                'slug' => Str::slug('Betanzos'), // Genera el slug a partir del nombre
            ],
            [
                'nombre' => 'Chaqui',
                'descripcion' => 'Municipio ubicado en el departamento de Potosí, Bolivia.',
                'direccion' => 'Plaza Principal, Chaqui',
                'telefono' => '+591 12345678',
                'email' => 'info@chaqui.gob.bo',
                'sitio_web' => 'www.chaqui.gob.bo',
                'latitud' => -18.123456,
                'longitud' => -65.789012,
                'poblacion' => 5000,
                'superficie' => 200.50,
                'historia' => 'Chaqui tiene una rica historia...',
                'gentilicio' => 'Chaqueños',
                'alcalde_nombre' => 'Juan Pérez',
                'alcalde_foto' => 'alcalde.jpg',
                'alcalde_descripcion' => 'El alcalde actual de Chaqui...',
                'user_id' => 1, // ID del usuario que crea el municipio (debes tener usuarios en tu tabla users)
                'slug' => Str::slug('Chaqui'), // Genera el slug a partir del nombre
            ],
            [
                'nombre' => 'Colcha "K"',
                'slug' => Str::slug('Colcha "K"'),
                'user_id' => 1, 
            ],
            [
                'nombre' => 'LLica',
                'slug' => Str::slug('LLica'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Mojinete',
                'slug' => Str::slug('Mojinete'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Porco',
                'slug' => Str::slug('Porco'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'San Agustin',
                'slug' => Str::slug('San Agustin'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'San Antonio de Esmoruco',
                'slug' => Str::slug('San Antonio de Esmoruco'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'San Pablo de Lipez',
                'slug' => Str::slug('San Pablo de Lipez'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'San Pedro de Quemes',
                'slug' => Str::slug('San Pedro de Quemes'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Tahua',
                'slug' => Str::slug('Tahua'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Tomave',
                'slug' => Str::slug('Tomave'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Uyuni',
                'slug' => Str::slug('Uyuni'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Atocha',
                'slug' => Str::slug('Atocha'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Cotagaita',
                'slug' => Str::slug('Cotagaita'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Tupiza',
                'slug' => Str::slug('Tupiza'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Villazon',
                'slug' => Str::slug('Villazon'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Vitichi',
                'slug' => Str::slug('Vitichi'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Uncia',
                'slug' => Str::slug('Uncia'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Caripuyo',
                'slug' => Str::slug('Caripuyo'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Chayanta',
                'slug' => Str::slug('Chayanta'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Chuquihuta',
                'slug' => Str::slug('Chuquihuta'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Colquechaca',
                'slug' => Str::slug('Colquechaca'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'LLallagua',
                'slug' => Str::slug('LLallagua'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Pocoata',
                'slug' => Str::slug('Pocoata'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Ravelo',
                'slug' => Str::slug('Ravelo'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'San Pedro de Macha',
                'slug' => Str::slug('San Pedro de Macha'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Sacaca',
                'slug' => Str::slug('Sacaca'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Acasio',
                'slug' => Str::slug('Acasio'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Belen de Urmiri',
                'slug' => Str::slug('Belen de Urmiri'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Caiza "D"',
                'slug' => Str::slug('Caiza "D"'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Ckochas',
                'slug' => Str::slug('Ckochas'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Potosí',
                'slug' => Str::slug('Potosí'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Puna',
                'slug' => Str::slug('Puna'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Tacobamba',
                'slug' => Str::slug('Tacobamba'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Tinguipaya',
                'slug' => Str::slug('Tinguipaya'),
                'user_id' => 1,
            ],
            [
                'nombre' => 'Yocalla',
                'slug' => Str::slug('Yocalla'),
                'user_id' => 1,
            ],
            // Añade más municipios aquí...
        ];

        // Itera sobre el array e inserta cada municipio en la base de datos
        foreach ($municipios as $municipio) {
            Municipio::create($municipio);
        }
    }
}
