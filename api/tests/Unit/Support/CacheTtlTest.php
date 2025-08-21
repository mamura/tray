<?php

namespace Tests\Unit\Support;

use App\Support\CacheTtl;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class CacheTtlTest extends TestCase
{
    public function test_ttl_uses_config_and_today_logic(): void
    {
        Config::set('app.timezone', 'America/Fortaleza');
        Config::set('sales.cache_ttl.today', 90);
        Config::set('sales.cache_ttl.past', 3600);

        $today = now('America/Fortaleza')->toDateString();

        $this->assertSame(90, CacheTtl::forReportDate($today));
        $this->assertSame(3600, CacheTtl::forReportDate('2001-01-01'));
    }
}
