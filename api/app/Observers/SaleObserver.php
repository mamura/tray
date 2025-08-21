<?php

namespace App\Observers;

use App\Models\Sale;
use App\Support\CacheKeys;
use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class SaleObserver
{
    public function created(Sale $sale): void
    {
        $this->invalidatePairs([[$sale->seller_id, $sale->sold_at]]);
    }

    public function updated(Sale $sale): void
    {
        $newSeller = (int) $sale->seller_id;
        $newDate   = $sale->sold_at;

        $oldSeller = (int) $sale->getOriginal('seller_id');
        $oldDate   = $sale->getOriginal('sold_at');

        $pairs = [[$newSeller, $newDate]];

        if ($oldDate && $oldDate !== $newDate) {
            $pairs[] = [$newSeller, $oldDate];
        }

        if ($oldSeller && $oldSeller !== $newSeller) {
            $pairs[] = [$oldSeller, $newDate];
        }

        if ($oldSeller && $oldSeller !== $newSeller && $oldDate && $oldDate !== $newDate) {
            $pairs[] = [$oldSeller, $oldDate];
        }

        $this->invalidatePairs($pairs);
    }

    public function deleted(Sale $sale): void
    {
        $this->invalidatePairs([[$sale->seller_id, $sale->sold_at]]);
    }

    protected function invalidatePairs(array $pairs): void
    {
        $dates   = [];
        $sellers = [];

        foreach ($pairs as [$sellerId, $date]) {
            $dates[]   = $this->ymd($date);
            $sellers[] = (int) $sellerId;
        }

        $dates      = array_values(array_unique($dates));
        $sellers    = array_values(array_unique($sellers));
        $taggable   = Cache::getStore() instanceof TaggableStore;

        if ($taggable) {
            // Redis/Memcached/Array: invalida por grupos
            foreach ($dates as $d) {
                Cache::tags(['reports', "date:{$d}"])->flush();
            }

            foreach ($sellers as $sid) {
                Cache::tags(['reports', "seller:{$sid}"])->flush();
            }

            return;
        }

        // Fallback sem tags: esquece chaves previsíveis
        foreach ($dates as $d) {
            Cache::forget(CacheKeys::adminDaily($d));
            Cache::forget(CacheKeys::sellersDailyList($d));
            Cache::forget(CacheKeys::sellersDailyListIncludingZero($d));

            foreach ($sellers as $sid) {
                Cache::forget(CacheKeys::sellerDaily($sid, $d));
            }
        }
    }

    protected function ymd(string|\DateTimeInterface $value): string
    {
        return Carbon::parse($value, config('app.timezone', 'UTC'))->toDateString();
    }
}
