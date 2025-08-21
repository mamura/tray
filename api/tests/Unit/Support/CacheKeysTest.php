<?php

namespace Tests\Unit\Support;

use App\Support\CacheKeys;
use PHPUnit\Framework\TestCase;

class CacheKeysTest extends TestCase
{
    public function test_admin_daily_key(): void
    {
        $this->assertSame('reports:admin:2025-01-10', CacheKeys::adminDaily('2025-01-10'));
    }

    public function test_seller_daily_key(): void
    {
        $this->assertSame('reports:seller:42:2025-01-10', CacheKeys::sellerDaily(42, '2025-01-10'));
    }

    public function test_sellers_daily_list_key(): void
    {
        $this->assertSame('reports:sellers:list:2025-01-10', CacheKeys::sellersDailyList('2025-01-10'));
    }

    public function test_sellers_daily_list_including_zero_key(): void
    {
        $this->assertSame(
            'reports:sellers:list:2025-01-10:includingZero',
            CacheKeys::sellersDailyListIncludingZero('2025-01-10')
        );
    }
}
