<?php

namespace App\Jobs;

use App\Models\Sale;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class DispatchSellerDailyCommissionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 30;

    public function __construct(
        public string $date,
        public bool $includeZero = false
    ) {}

    public function handle(): void
    {
        if ($this->includeZero) {
            $ids = \App\Models\Seller::query()->pluck('id');
            $this->dispatchMany($ids);
            return;
        }

        $ids = Sale::query()
            ->whereDate('sold_at', $this->date)
            ->distinct()
            ->pluck('seller_id');

        $this->dispatchMany($ids);
    }

    protected function dispatchMany(Collection $sellerIds): void
    {
        $sellerIds->chunk(100)->each(function ($chunk) {
            foreach ($chunk as $sellerId) {
                SendSellerDailyCommissionMailJob::dispatch((int) $sellerId, $this->date)->onQueue('default');
            }
        });

        Log::info('DispatchSellerDailyCommissionsJob queued', [
            'date' => $this->date, 'count' => $sellerIds->count(), 'includeZero' => $this->includeZero,
        ]);
    }
}
