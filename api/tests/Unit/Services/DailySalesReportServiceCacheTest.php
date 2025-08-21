<?php

namespace Tests\Unit\Services;

use App\Data\Sales\AdminDailySummary;
use App\Services\Sales\CommissionService;
use App\Services\Sales\DailySalesReportService;
use App\Services\Sales\SalesQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\TestCase;

class DailySalesReportServiceCacheTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('app.timezone', 'America/Fortaleza');
        Config::set('cache.default', 'array');
    }

    public function test_admin_summary_is_cached_and_reused(): void
    {
        $date = '2025-01-10';

        $query = Mockery::mock(SalesQuery::class);
        $query->shouldReceive('totalsForDate')
              ->once()
              ->with($date)
              ->andReturn(['count' => 2, 'sum' => 300.0]);

        $svc = new DailySalesReportService($query, new CommissionService);

        $first  = $svc->adminSummary($date);
        $second = $svc->adminSummary($date);

        $this->assertInstanceOf(AdminDailySummary::class, $first);
        $this->assertSame(2, $first->totalCount);
        $this->assertSame(300.0, $first->totalAmount);
        $this->assertSame(25.5, $first->totalCommission);

        $this->assertEquals($first->toArray(), $second->toArray());
    }
}
