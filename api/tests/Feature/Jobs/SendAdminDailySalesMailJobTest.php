<?php

namespace Tests\Feature\Jobs;

use App\Jobs\SendAdminDailySalesMailJob;
use App\Mail\AdminDailySalesMail;
use App\Models\Sale;
use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendAdminDailySalesMailJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_sends_admin_mail_with_summary(): void
    {
        Config::set('app.timezone', 'America/Fortaleza');
        Config::set('sales.admin_email', 'admin@example.test');

        $today  = now('America/Fortaleza')->toDateString();
        $seller = Seller::factory()->create();
        Sale::factory()->create([
            'seller_id' => $seller->id,
            'sold_at'   => $today,
            'amount'    => 123.45,
        ]);

        Mail::fake();

        $job = new SendAdminDailySalesMailJob($today, true);

        app()->call([$job, 'handle']);

        // Se o job usa Mail::queue(...)
        //Mail::assertQueued(AdminDailySalesMail::class, 1);

        // Se usa Mail::send(...), troque por:
         Mail::assertSent(AdminDailySalesMail::class, 1);
    }
}
