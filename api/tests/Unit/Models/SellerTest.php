<?php

namespace Tests\Unit\Models;

use App\Models\Sale;
use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SellerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function seller_has_many_sales()
    {
        $seller = Seller::factory()->create();
        Sale::factory()->count(3)->create(['seller_id' => $seller->id]);

        $this->assertSame(3, $seller->sales()->count());
    }

    #[Test]
    public function salesOn_returns_sales_only_for_that_date()
    {
        $seller = Seller::factory()->create();
        $today  = Carbon::today()->toDateString();
        $yday   = Carbon::yesterday()->toDateString();

        Sale::factory()->count(2)->create(['seller_id' => $seller->id, 'sold_at' => $today]);
        Sale::factory()->create(['seller_id' => $seller->id, 'sold_at' => $yday]);

        $this->assertSame(2, $seller->salesOn($today)->count());
        $this->assertSame(1, $seller->salesOn($yday)->count());
    }
}
