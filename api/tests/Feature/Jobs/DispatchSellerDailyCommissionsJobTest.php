<?php

namespace Tests\Feature\Jobs;

use App\Jobs\DispatchSellerDailyCommissionsJob;
use App\Jobs\SendSellerDailyCommissionMailJob;
use App\Models\Sale;
use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class DispatchSellerDailyCommissionsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_dispatches_one_job_per_seller_that_sold_today(): void
    {
        Config::set('app.timezone', 'America/Fortaleza');
        $today = now('America/Fortaleza')->toDateString();

        $a = Seller::factory()->create();
        $b = Seller::factory()->create();
        $c = Seller::factory()->create(); // não vende

        Sale::factory()->create(['seller_id' => $a->id, 'sold_at' => $today, 'amount' => 100]);
        Sale::factory()->create(['seller_id' => $b->id, 'sold_at' => $today, 'amount' => 200]);

        Bus::fake();

        $job = new DispatchSellerDailyCommissionsJob($today);
        $job->handle();

        Bus::assertDispatched(SendSellerDailyCommissionMailJob::class, 2);
        Bus::assertNotDispatched(function (SendSellerDailyCommissionMailJob $job) use ($c, $today) {
            return $job->sellerId === $c->id && $job->date === $today;
        });
    }
}
