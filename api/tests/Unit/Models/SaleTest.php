<?php

namespace Tests\Unit\Models;

use App\Models\Sale;
use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SaleTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function commission_acessor_calculates_8_5_percent()
    {
        $seller = Seller::factory()->create();
        $sale = Sale::factory()->create([
            'seller_id' => $seller->id,
            'amount'    => 100.00,
            'sold_at'   => Carbon::today()->toDateString(),
        ]);

        $this->assertSame(8.5, $sale->commission);
    }

    #[Test]
    public function sale_belongs_to_a_seller()
    {
        $sale = Sale::factory()->create();

        $this->assertInstanceOf(Seller::class, $sale->seller);
        $this->assertTrue($sale->seller->exists);
    }

    #[Test]
    public function scope_ofSeller_and_soldBetween_filter_correctly()
    {
        $sellerA = Seller::factory()->create();
        $sellerB = Seller::factory()->create();

        // 3 vendas para A (duas hoje, uma ontem)
        Sale::factory()->create(['seller_id' => $sellerA->id, 'sold_at' => Carbon::today()->toDateString()]);
        Sale::factory()->create(['seller_id' => $sellerA->id, 'sold_at' => Carbon::today()->toDateString()]);
        Sale::factory()->create(['seller_id' => $sellerA->id, 'sold_at' => Carbon::yesterday()->toDateString()]);

        // 2 vendas para B (hoje)
        Sale::factory()->count(2)->create(['seller_id' => $sellerB->id, 'sold_at' => Carbon::today()->toDateString()]);

        $today = Carbon::today()->toDateString();
        $yday  = Carbon::yesterday()->toDateString();

        // filtro por vendedor
        $this->assertSame(3, Sale::ofSeller($sellerA->id)->count());
        $this->assertSame(2, Sale::ofSeller($sellerB->id)->count());

        // filtro por período: ontem + hoje totalizam 5
        $this->assertSame(5, Sale::soldBetween($yday, $today)->count());

        // apenas hoje
        $this->assertSame(4, Sale::soldBetween($today, $today)->count());
    }
}
