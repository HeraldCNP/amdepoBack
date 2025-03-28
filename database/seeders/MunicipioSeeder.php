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
            ],
            [
                'nombre' => 'LLica',
            ],
            [
                'nombre' => 'Mojinete',
            ],
            [
                'nombre' => 'Porco',
            ],
            [
                'nombre' => 'San Agustin',
            ],
            [
                'nombre' => 'San Antonio de Esmoruco',
            ],
            [
                'nombre' => 'San Pablo de Lipez',
            ],
            [
                'nombre' => 'San Pedro de Quemes',
            ],
            [
                'nombre' => 'Tahua',
            ],
            [
                'nombre' => 'Tomave',
            ],
            [
                'nombre' => 'Uyuni',
            ],
            [
                'nombre' => 'Atocha',
            ],
            [
                'nombre' => 'Cotagaita',
            ],
            [
                'nombre' => 'Tupiza',
            ],
            [
                'nombre' => 'Villazon',
            ],
            [
                'nombre' => 'Vitichi',
            ],
            [
                'nombre' => 'Uncia',
            ],
            [
                'nombre' => 'Caripuyo',
            ],
            [
                'nombre' => 'Chayanta',
            ],
            [
                'nombre' => 'Chuquihuta',
            ],
            [
                'nombre' => 'Colquechaca',
            ],
            [
                'nombre' => 'LLallagua',
            ],
            [
                'nombre' => 'Pocoata',
            ],
            [
                'nombre' => 'Ravelo',
            ],
            [
                'nombre' => 'San Pedro de Macha',
            ],
            [
                'nombre' => 'Sacaca',
            ],
            [
                'nombre' => 'Acasio',
            ],
            [
                'nombre' => 'Belen de Urmiri',
            ],
            [
                'nombre' => 'Caiza "D"',
            ],
            [
                'nombre' => 'Ckochas',
            ],
            [
                'nombre' => 'Potosí',
            ],
            [
                'nombre' => 'Puna',
            ],
            [
                'nombre' => 'Tacobamba',
            ],
            [
                'nombre' => 'Tinguipaya',
            ],
            [
                'nombre' => 'Yocalla',
            ],
            // Añade más municipios aquí...
        ];

        // Itera sobre el array e inserta cada municipio en la base de datos
        foreach ($municipios as $municipio) {
            Municipio::create($municipio);
        }
    }
}
