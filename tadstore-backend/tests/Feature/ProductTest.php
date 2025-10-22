<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_product_successfully()
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);

        $category = \App\Models\Category::factory()->create();

        $this->actingAs($admin, 'sanctum');

        $response = $this->postJson('/api/products', [
            'name' => 'Producto Test',
            'description' => 'Descripción',
            'price' => 100,
            'stock' => 5,
            'category_id' => $category->id,
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['id', 'name', 'price']);
    }

    /** @test */
    public function it_requires_name_field()
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/products', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function admin_can_update_a_product()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->actingAs($admin, 'sanctum');

        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => 'Actualizado',
            'price' => 99,
            'stock' => 10,
            'category_id' => $category->id
        ]);

        $response->assertStatus(200)
                ->assertJsonFragment(['name' => 'Actualizado']);
    }

}
