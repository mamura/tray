<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\Seller;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        // Garante que existem sellers
        if (Seller::count() === 0) {
            $this->call(SellerSeeder::class);
        }

        $sellerIds = Seller::query()->pluck('id')->all();

        // 100 vendas; cada venda recebe um seller aleatório
        // (sold_at e amount já são definidos pela Factory)
        Sale::factory()
            ->count(100)
            ->state(fn () => ['seller_id' => fake()->randomElement($sellerIds)])
            ->create();
    }
}
