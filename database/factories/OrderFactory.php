<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'ORDER-' . rand(11111, 99999),
            'user_id' => User::factory(),
            'total_price' => rand(10000, 100000),
            'status' => 'pending',
            'midtrans_payment_type' => null,
            'midtrans_payment_url' => $this->faker->url,
            'midtrans_snap_token' => $this->faker->word,
        ];
    }
}

