<?php

namespace Tests\Unit\Services;

use App\Models\Sale;
use App\Models\Seller;
use App\Services\Sales\CommissionService;
use App\Services\Sales\DailySalesReportService;
use App\Services\Sales\SalesQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DailySalesReportServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function builds_seller_and_admin_summaries(): void
    {
        $a = Seller::factory()->create(['name'=>'Alice','email'=>'alice@test.dev']);
        $b = Seller::factory()->create();

        // dia alvo
        Sale::factory()->create(['seller_id'=>$a->id,'amount'=>100,'sold_at'=>'2025-01-10']);
        Sale::factory()->create(['seller_id'=>$a->id,'amount'=>200,'sold_at'=>'2025-01-10']);
        Sale::factory()->create(['seller_id'=>$b->id,'amount'=>300,'sold_at'=>'2025-01-10']);
        // fora do dia
        Sale::factory()->create(['seller_id'=>$a->id,'amount'=>999,'sold_at'=>'2025-01-09']);

        $svc = new DailySalesReportService(new SalesQuery(), new CommissionService());

        $sellerSummary = $svc->sellerSummary($a->id, '2025-01-10');
        $this->assertSame('2025-01-10', $sellerSummary->date);
        $this->assertSame(2, $sellerSummary->count);
        $this->assertSame(300.0, $sellerSummary->totalAmount);
        $this->assertSame(25.5, $sellerSummary->totalCommission);

        $admin = $svc->adminSummary('2025-01-10');
        $this->assertSame(3, $admin->totalCount);
        $this->assertSame(600.0, $admin->totalAmount);
        $this->assertSame(51.0, $admin->totalCommission);

        $all = $svc->allSellersSummaries('2025-01-10');
        $this->assertCount(2, $all);
    }
}
