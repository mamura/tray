<?php

namespace App\Data\Sales;

class AdminDailySummary
{
    public function __construct(
        public string $date,
        public int $totalCount,
        public float $totalAmount,
        public float $totalCommission
    ) {}

    public function toArray(): array
    {
        return [
            'date'             => $this->date,
            'total_count'      => $this->totalCount,
            'total_amount'     => $this->totalAmount,
            'total_commission' => $this->totalCommission,
        ];
    }
}
