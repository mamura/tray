<?php

namespace Tests\Unit\Services;

use App\Services\Sales\CommissionService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CommissionServiceTest extends TestCase
{
    #[Test]
    public function calculates_with_default_rate(): void
    {
        config()->set('sales.commission_rate', 0.085);
        $svc = new CommissionService();

        $this->assertSame(8.5,  $svc->forAmount(100.00));
        $this->assertSame(17.0, $svc->forTotalAmount(200.00));
    }

    #[Test]
    public function calculates_with_custom_rate_from_config(): void
    {
        config()->set('sales.commission_rate', 0.10);
        $svc = new CommissionService();

        $this->assertSame(10.0, $svc->forAmount(100.00));
    }
}
