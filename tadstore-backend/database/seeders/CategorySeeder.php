<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $categories = [
            ['name' => 'Tecnología', 'description' => 'Dispositivos electrónicos y gadgets', 'slug' => 'tecnologia'],
            ['name' => 'Moda', 'description' => 'Ropa, calzado y accesorios de moda', 'slug' => 'moda'],
            ['name' => 'Hogar', 'description' => 'Muebles, decoración y electrodomésticos', 'slug' => 'hogar'],
            ['name' => 'Deportes', 'description' => 'Equipamiento y ropa deportiva', 'slug' => 'deportes'],
            ['name' => 'Juguetes', 'description' => 'Juguetes y juegos para niños de todas las edades', 'slug' => 'juguetes'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
