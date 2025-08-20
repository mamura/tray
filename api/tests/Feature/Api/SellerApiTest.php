<?php

namespace Tests\Feature\Api;

use App\Models\Sale;
use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SellerApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_crud_sellers(): void
    {
        // create
        $res = $this->postJson('/api/v1/sellers', [
            'name'  => 'Bob',
            'email' => 'bob@example.test',
        ])->assertCreated();

        $json = $res->json();
        $id   = data_get($json, 'data.id', data_get($json, 'id'));
        $this->assertNotNull($id);

        $this->assertDatabaseHas('sellers', ['id'=>$id,'email'=>'bob@example.test']);

        // index
        $this->getJson('/api/v1/sellers?per_page=10')
             ->assertOk()
             ->assertJsonStructure(['data']);

        // show
        $res = $this->getJson("/api/v1/sellers/{$id}")->assertOk();
        $this->assertSame('bob@example.test', data_get($res->json(),'data.email', data_get($res->json(),'email')));

        // update
        $res = $this->putJson("/api/v1/sellers/{$id}", [
            'name'  => 'Bobby',
            'email' => 'bobby@example.test',
        ])->assertOk();

        $this->assertSame('Bobby', data_get($res->json(),'data.name', data_get($res->json(),'name')));

        // cascade delete (remove vendas junto)
        Sale::factory()->count(2)->create(['seller_id'=>$id]);

        $this->deleteJson("/api/v1/sellers/{$id}")->assertNoContent();
        $this->assertDatabaseMissing('sellers',['id'=>$id]);
        $this->assertDatabaseCount('sales', 0);
    }

    #[Test]
    public function validation_errors_on_create(): void
    {
        $this->postJson('/api/v1/sellers', [])
             ->assertStatus(422)
             ->assertJsonValidationErrors(['name','email']);
    }
}
