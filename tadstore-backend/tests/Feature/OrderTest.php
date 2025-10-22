<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Order;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_request_order_code()
    {
        $product = \App\Models\Product::factory()->create();

        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+573001234567',
            'address' => 'Calle Falsa 123',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ],
        ];

        $response = $this->postJson('/api/orders/send-code', $data);

        $response->assertStatus(200)
                ->assertJsonStructure(['message']);
    }


    /** @test */
    public function user_can_confirm_order_with_code()
    {
        $product = \App\Models\Product::factory()->create();

        // 🟢 Simular verificación previa
        \App\Models\OrderVerification::create([
            'email' => 'bernardo@example.com',
            'code' => 'ABC123',
            'expires_at' => now()->addMinutes(10),
        ]);

        $data = [
            'name' => 'Bernardo',
            'email' => 'bernardo@example.com',
            'phone' => '+573001234567',
            'address' => 'Calle 45 #123',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
            'code' => 'ABC123', // ✅ Campo esperado por el controlador
        ];

        $response = $this->postJson('/api/orders/confirm', $data);

        $response->assertStatus(200)
                ->assertJsonStructure(['message', 'order']);

        $this->assertDatabaseHas('orders', [
            'email' => 'bernardo@example.com',
            'confirmation_status' => true,
            'status' => 'confirmed',
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
        ]);
    }


}
