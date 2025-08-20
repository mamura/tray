<?php

namespace Tests\Feature\Api;

use App\Models\Sale;
use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CommissionApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function resend_endpoint_returns_summary(): void
    {
        $seller = Seller::factory()->create(['email'=>'mail@test.dev']);
        Sale::factory()->create(['seller_id'=>$seller->id,'amount'=>100,'sold_at'=>'2025-01-10']);

        $this->postJson("/api/v1/sellers/{$seller->id}/commission/resend", ['date'=>'2025-01-10'])
             ->assertStatus(202)
             ->assertJsonPath('summary.seller_id', $seller->id)
             ->assertJsonPath('summary.total_amount', 100)
             ->assertJsonPath('summary.total_commission', 8.5);
    }
}
