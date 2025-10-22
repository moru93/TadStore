<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Category;

class CategoryModelTest extends TestCase
{
    /** @test */
    public function it_creates_a_category_instance_with_valid_attributes()
    {
        $category = new Category([
            'name' => 'Test Category',
            'description' => 'Descripción de prueba',
            'slug' => 'test-category',
        ]);

        // ✅ Comprobaciones de atributos
        $this->assertEquals('Test Category', $category->name);
        $this->assertEquals('Descripción de prueba', $category->description);
        $this->assertEquals('test-category', $category->slug);

        // ✅ Verifica que los campos sean fillable
        foreach (['name', 'description', 'slug'] as $field) {
            $this->assertTrue($category->isFillable($field), "El campo {$field} no es fillable.");
        }
    }
}
