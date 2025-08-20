<?php

namespace App\Observers;

use App\Models\Sale;
use App\Support\CacheKeys;
use Illuminate\Support\Facades\Cache;

class SaleObserver
{
    public function created(Sale $sale): void
    {
        $this->forgetKeys($sale->seller_id, $sale->sold_at);
    }

    public function updated(Sale $sale): void
    {
        $oldDate    = $sale->getOriginal('sold_at');
        $oldSeller  = (int) $sale->getOriginal('seller_id');

        $this->forgetKeys($sale->seller_id, $sale->sold_at);

        if ($oldDate && $oldDate !== $sale->sold_at) {
            $this->forgetKeys($sale->seller_id, $oldDate);
        }
        if ($oldSeller && $oldSeller !== $sale->seller_id) {
            $this->forgetKeys($oldSeller, $sale->sold_at);
        }
    }

    public function deleted(Sale $sale): void
    {
        $this->forgetKeys($sale->seller_id, $sale->sold_at);
    }

    protected function forgetKeys(int $sellerId, string $date): void
    {
        Cache::forget(CacheKeys::adminDaily($date));
        Cache::forget(CacheKeys::sellerDaily($sellerId, $date));
        Cache::forget(CacheKeys::sellersDailyList($date));
    }
}
