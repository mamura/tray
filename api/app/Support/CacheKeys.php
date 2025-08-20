<?php

namespace App\Support;

final class CacheKeys
{
    public static function adminDaily(string $date): string
    {
        return "reports:admin:{$date}";
    }

    public static function sellerDaily(int $sellerId, string $date): string
    {
        return "reports:seller:{$sellerId}:{$date}";
    }

    public static function sellersDailyList(string $date): string
    {
        return "reports:sellers:list:{$date}";
    }

    public static function sellersDailyListIncludingZero(string $date): string
    {
        return "reports:sellers:list:{$date}:includingZero";
    }
}
