<?php

namespace Database\Seeders;

use App\Models\Seller;
use Illuminate\Database\Seeder;

class SellerSeeder extends Seeder
{
    public function run(): void
    {
        // 10 vendedores com e-mails únicos (Factory já faz unique)
        Seller::factory()->count(10)->create();
    }
}
