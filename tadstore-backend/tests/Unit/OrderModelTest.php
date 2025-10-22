<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Order;

class OrderModelTest extends TestCase
{
    /** @test */
    public function it_creates_an_order_instance_with_valid_attributes()
    {
        $order = new Order([
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'phone' => '3001234567',
            'address' => 'Calle Falsa 123',
            'total_amount' => 199.99,
            'status' => 'pending',
            'confirmation_code' => 'ABC123',
            'confirmation_status' => 'pending',
        ]);

        $this->assertEquals('Juan Pérez', $order->name);
        $this->assertEquals(199.99, $order->total_amount);
        $this->assertEquals('pending', $order->status);

        foreach (['name', 'email', 'phone', 'address', 'total_amount', 'status', 'confirmation_code', 'confirmation_status'] as $field) {
            $this->assertTrue($order->isFillable($field), "El campo {$field} no es fillable.");
        }
    }
}
