<?php

namespace App\Jobs;

use App\Mail\AdminDailySalesMail;
use App\Services\Sales\DailySalesReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendAdminDailySalesMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries       = 3;
    public int $backoff     = 30;

    public function __construct(
        public string $date,
        public bool $withBreakdown = true
    ) {}

    public function handle(DailySalesReportService $reports): void
    {
        $admin = $reports->adminSummary($this->date);
        $items = $this->withBreakdown ? $reports->allSellersSummaries($this->date)->all() : [];
        $to    = (string) config('sales.admin_email', config('mail.from.address'));

        Mail::to($to)->send(new AdminDailySalesMail($admin, $items));
    }

    public function failed(Throwable $e): void
    {
        Log::error('SendAdminDailySalesMailJob failed', [
            'date' => $this->date, 'error' => $e->getMessage(),
        ]);
    }
}
