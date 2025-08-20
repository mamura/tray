<?php

namespace App\Jobs;

use App\Mail\SellerDailyCommissionMail;
use App\Services\Sales\DailySalesReportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendSellerDailyCommissionMailJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public int $tries       = 3;
    public int $backoff     = 30;

    public function __construct(
        public int $sellerId,
        public string $date
    ) {}

    public function handle(DailySalesReportService $reports): void
    {
        $summary = $reports->sellerSummary($this->sellerId, $this->date);
        Mail::to($summary->sellerEmail)->send(new SellerDailyCommissionMail($summary));
    }

    public function failed(Throwable $e): void
    {
        Log::error('SendSellerDailyCommissionMailJob failed', [
            'seller_id' => $this->sellerId,
            'date'      => $this->date,
            'error'     => $e->getMessage()
        ]);
    }
}
