<?php

namespace App\Support;

use DateTimeInterface;
use Illuminate\Support\Carbon;

class CacheTtl
{
    public static function forReportDate(string|DateTimeInterface $date, ?string $tz = null): int
    {
        $tz ??= config('app.timezone');
        $d = $date instanceof \DateTimeInterface
            ? Carbon::parse($date, $tz)->toDateString()
            : (string) $date;

        $today = now($tz)->toDateString();

        $todayTtl = (int) config('sales.cache_ttl.today', 60);
        $pastTtl  = (int) config('sales.cache_ttl.past', 60*60*24*30);

        return $d === $today ? $todayTtl : $pastTtl;
    }
}
