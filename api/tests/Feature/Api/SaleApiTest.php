<?php

namespace Tests\Feature\Api;

use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SaleApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_crud_sales_and_filter(): void
    {
        $seller = Seller::factory()->create();

        // create
        $res = $this->postJson('/api/v1/sales', [
            'seller_id' => $seller->id,
            'amount'    => 123.45,
            'sold_at'   => '2025-01-10',
        ])->assertCreated();

        $json = $res->json();
        $saleId = data_get($json,'data.id', data_get($json,'id'));
        $this->assertNotNull($saleId);

        // list
        $this->getJson('/api/v1/sales?per_page=5')->assertOk()->assertJsonStructure(['data']);

        // show
        $res = $this->getJson("/api/v1/sales/{$saleId}")->assertOk();
        $this->assertSame(123.45, (float) data_get($res->json(),'data.amount', data_get($res->json(),'amount')));

        // update
        $res = $this->putJson("/api/v1/sales/{$saleId}", [
            'amount'  => 200.00,
            'sold_at' => '2025-01-10',
        ])->assertOk();
        $this->assertSame(200.0, (float) data_get($res->json(),'data.amount', data_get($res->json(),'amount')));

        // by seller
        $this->getJson("/api/v1/sellers/{$seller->id}/sales?date_from=2025-01-10&date_to=2025-01-10")
             ->assertOk()
             ->assertJsonStructure(['data']);

        // delete
        $this->deleteJson("/api/v1/sales/{$saleId}")->assertNoContent();
        $this->assertDatabaseMissing('sales',['id'=>$saleId]);
    }

    #[Test]
    public function validation_errors_on_create(): void
    {
        $this->postJson('/api/v1/sales', [])
             ->assertStatus(422)
             ->assertJsonValidationErrors(['seller_id','amount','sold_at']);
    }
}
