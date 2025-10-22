<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;

class ProductModelTest extends TestCase
{
    /** @test */
    public function it_creates_a_product_instance_with_valid_attributes()
    {
        $product = new Product([
            'category_id' => 1,
            'name' => 'Test Product',
            'description' => 'Descripción de prueba',
            'price' => 99.99,
            'stock' => 10,
            'image_url' => 'https://example.com/image.jpg',
        ]);

        // ✅ Validaciones de valores
        $this->assertEquals(1, $product->category_id);
        $this->assertEquals('Test Product', $product->name);
        $this->assertEquals('Descripción de prueba', $product->description);
        $this->assertEquals(99.99, $product->price);
        $this->assertEquals(10, $product->stock);
        $this->assertEquals('https://example.com/image.jpg', $product->image_url);

        // ✅ Verificar los campos fillable
        foreach (['category_id', 'name', 'description', 'price', 'stock', 'image_url'] as $field) {
            $this->assertTrue($product->isFillable($field), "El campo {$field} no es fillable.");
        }
    }
}
