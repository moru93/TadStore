<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $products = [
            ['name' => 'Smartphone XYZ', 'description' => 'Un smartphone de última generación con pantalla OLED y cámara de alta resolución.', 'price' => 699.99, 'stock' => 50, 'category_id' => 1],
            ['name' => 'Laptop ABC', 'description' => 'Una laptop potente para profesionales con procesador Intel i7 y 16GB de RAM.', 'price' => 1199.99, 'stock' => 30, 'category_id' => 1],
            ['name' => 'Camiseta de Algodón', 'description' => 'Camiseta cómoda y transpirable, perfecta para el día a día.', 'price' => 19.99, 'stock' => 100, 'category_id' => 2],
            ['name' => 'Zapatillas Deportivas', 'description' => 'Zapatillas ligeras y resistentes para correr y entrenar.', 'price' => 89.99, 'stock' => 75, 'category_id' => 2],
            ['name' => 'Sofá de Cuero', 'description' => 'Sofá elegante y cómodo hecho de cuero genuino.', 'price' => 499.99, 'stock' => 20, 'category_id' => 3],
            ['name' => 'Mesa de Centro', 'description' => 'Mesa de centro moderna con acabado en madera y metal.', 'price' => 149.99, 'stock' => 40, 'category_id' => 3],
            ['name' => 'Bicicleta de Montaña', 'description' => 'Bicicleta robusta diseñada para terrenos difíciles.', 'price' => 299.99, 'stock' => 25, 'category_id' => 4],
            ['name' => 'Balón de Fútbol', 'description' => 'Balón oficial para partidos y entrenamientos.', 'price' => 29.99, 'stock' => 60, 'category_id' => 4],
            ['name' => 'Juego de Construcción', 'description' => 'Juego educativo para desarrollar habilidades motoras y creatividad.', 'price' => 39.99, 'stock' => 80, 'category_id' => 5],
            ['name' => 'Muñeca Interactiva', 'description' => 'Muñeca que habla y canta, ideal para niñas y niños.', 'price' => 49.99, 'stock' => 45, 'category_id' => 5],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
