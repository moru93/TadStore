<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'total_amount' => $this->faker->randomFloat(2, 10, 1000),
            'confirmation_code' => strtoupper(Str::random(6)),
            'confirmation_status' => 'pending',
        ];
    }
}
