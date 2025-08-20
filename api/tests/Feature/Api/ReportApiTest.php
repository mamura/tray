<?php

namespace Tests\Feature\Api;

use App\Models\Sale;
use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReportApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_and_seller_daily_reports(): void
    {
        $a = Seller::factory()->create();
        $b = Seller::factory()->create();

        Sale::factory()->create(['seller_id'=>$a->id,'amount'=>100,'sold_at'=>'2025-01-10']);
        Sale::factory()->create(['seller_id'=>$b->id,'amount'=>200,'sold_at'=>'2025-01-10']);
        Sale::factory()->create(['seller_id'=>$a->id,'amount'=>999,'sold_at'=>'2025-01-09']); // fora

        // admin
        $this->getJson('/api/v1/reports/daily/admin?date=2025-01-10')
             ->assertOk()
             ->assertJsonPath('total_count', 2)
             ->assertJsonPath('total_amount', 300)
             ->assertJsonPath('total_commission', 25.5);

        // sellers
        $this->getJson('/api/v1/reports/daily/sellers?date=2025-01-10')
             ->assertOk()
             ->assertJsonStructure(['date','items']);

        // seller único
        $this->getJson("/api/v1/reports/daily/sellers/{$a->id}?date=2025-01-10")
             ->assertOk()
             ->assertJsonPath('seller_id', $a->id)
             ->assertJsonPath('total_amount', 100);
    }
}
