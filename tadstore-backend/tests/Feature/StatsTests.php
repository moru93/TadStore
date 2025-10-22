<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StatsTests extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function admin_can_access_order_stats()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'sanctum');

        $response = $this->getJson('/api/orders/stats');

        $response->assertStatus(200)
                ->assertJsonStructure(['total_orders', 'total_sales']);
    }

    /** @test */
    public function non_admin_cannot_view_sales_stats()
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/stats/sales');

        $response->assertStatus(403);
    }
}