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
                'nombre' => 'Potosí',
                'descripcion' => 'Ciudad colonial y capital del departamento de Potosí, famosa por su riqueza minera en la época de la colonia y su Cerro Rico.',
                'provincia' => 'Tomás Frías',
                'slug' => Str::slug('Potosi'),
                'circuscripcion' => 'Circunscripción 36',
                'comunidades' => 'Cantumarca, San Pedro, La Banda',
                'aniversario' => '10-11',
                'fiestaPatronal' => '15-08',
                'ferias' => 'Feria Artesanal, Feria del Libro (ejemplo)',
                'direccion' => 'Plaza 10 de Noviembre, Potosí',
                'telefono' => '+591 2 6222222',
                'email' => 'gob.mun.potosi@gmail.com',
                'sitio_web' => 'www.potosi.gob.bo',
                'facebook' => 'https://www.facebook.com/PotosiGob/',
                'latitud' => -19.5222,
                'longitud' => -65.7533,
                'poblacion' => 240000,
                'superficie' => 1182.2,
                'historia' => 'Fundada en 1545, Potosí fue una de las ciudades más grandes y ricas del mundo gracias a la plata del Cerro Rico, declarado Patrimonio de la Humanidad por la UNESCO.',
                'gentilicio' => 'Potosinos',
                'alcalde_nombre' => 'Jhonny Mamani',
                'alcalde_foto' => 'alcalde_jhonny_mamani.jpg',
                'user_id' => 1,

            ],
            [
                'nombre' => 'Belen de Urmiri',
                'descripcion' => 'Ciudad colonial y capital del departamento de Potosí, famosa por su riqueza minera en la época de la colonia y su Cerro Rico.',
                'slug' => Str::slug('Belen de Urmiri'),
                'provincia' => 'Tomás Frías', // Nuevo campo
                'circuscripcion' => '36', // Nuevo campo (ejemplo)
                'comunidades' => '31 comunidades', // Nuevo campo (ejemplo)
                'aniversario' => '10 de noviembre de 1993',
                'fiestaPatronal' => '8 de septiembre Festividad de la Virgen de Guadalupe',
                'ferias' => '12 y 13 de junio Expo Feria Municipal Agro Camelido y Cultural de Belen de Urmiri', // Nuevo campo
                'direccion' => 'Carretera hacia Oruro. Edificio Oficinas de la alcaldía s/n. Comunidad Belen de Urmiri a una hora y media de la ciudad de Potosí',
                'telefono' => '+591 72375165',
                'email' => 'quispemartin427@gmail.com',
                'sitio_web' => '',
                'facebook' => 'https://www.facebook.com/profile.php?id=61561120649222',
                'latitud' => -19.5222, // Latitud aproximada de Potosí
                'longitud' => -65.7533, // Longitud aproximada de Potosí
                'poblacion' => 240000, // Población estimada
                'superficie' => 1182.2, // Superficie aproximada en km²
                'historia' => '',
                'gentilicio' => '',
                'alcalde_nombre' => 'Martin Quispe Mamani',
                'alcalde_foto' => 'alcalde_jhonny_mamani.jpg',
                'user_id' => 1,

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
