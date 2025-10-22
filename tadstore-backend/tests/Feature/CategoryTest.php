<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_create_category()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin, 'sanctum');

        $response = $this->postJson('/api/categories', [
            'name' => 'Nueva categoría',
            'description' => 'Texto de prueba',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'category' => ['id', 'name', 'description', 'slug']
            ]);
    }

    /** @test */
    public function it_lists_categories()
    {
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'current_page',
                    'data',
                    'first_page_url',
                    'last_page',
                    'total'
                ])
                ->assertJsonCount(3, 'data');
    }

}