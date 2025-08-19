<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        return [
            'seller_id' => Seller::factory(),
            'amount'    => fake()->randomFloat(2, 10, 2000),
            'sold_at'   => fake()->dateTimeBetween('-15 days', 'now')->format('Y-m-d'),
        ];
    }
}
