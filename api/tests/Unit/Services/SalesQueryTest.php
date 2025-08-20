<?php

namespace Tests\Unit\Services;

use App\Models\Sale;
use App\Models\Seller;
use App\Services\Sales\SalesQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SalesQueryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function totals_and_group_by_for_date(): void
    {
        $a = Seller::factory()->create();
        $b = Seller::factory()->create();

        // 2025-01-10: A(100,200) B(300,400)  => total dia = 1000 (4 vendas)
        Sale::factory()->create(['seller_id'=>$a->id,'amount'=>100,'sold_at'=>'2025-01-10']);
        Sale::factory()->create(['seller_id'=>$a->id,'amount'=>200,'sold_at'=>'2025-01-10']);
        Sale::factory()->create(['seller_id'=>$b->id,'amount'=>300,'sold_at'=>'2025-01-10']);
        Sale::factory()->create(['seller_id'=>$b->id,'amount'=>400,'sold_at'=>'2025-01-10']);

        // fora do dia
        Sale::factory()->create(['seller_id'=>$a->id,'amount'=>50,'sold_at'=>'2025-01-09']);

        $svc = new SalesQuery();

        $totAll = $svc->totalsForDate('2025-01-10');
        $this->assertSame(4, $totAll['count']);
        $this->assertSame(1000.0, $totAll['sum']);

        $totA = $svc->totalsForDate('2025-01-10', $a->id);
        $this->assertSame(2, $totA['count']);
        $this->assertSame(300.0, $totA['sum']);

        $rows = $svc->groupBySellerForDate('2025-01-10');
        $this->assertCount(2, $rows);
        $this->assertEqualsCanonicalizing(
            [$a->id, $b->id],
            $rows->pluck('seller_id')->all()
        );
    }
}
