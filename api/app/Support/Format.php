<?php

namespace App\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class Format
{
    public static function dateBR(string|CarbonInterface|null $date, string $fallback = ''): string
    {
        if (empty($date)) {
            return $fallback;
        }

        $c = $date instanceof CarbonInterface ? $date : Carbon::parse($date);

        return $c->format('d/m/Y');
    }

    public static function moneyBR(float|int|string|null $value, string $currency = 'R$'): string
    {
        $v = (float) $value;
        return $currency.' '.number_format($v, 2, ',', '.');
    }

    public static function percentBR(float $rate, int $decimals = 1): string
    {
        return number_format($rate * 100, $decimals, ',', '.').'%';
    }
}
