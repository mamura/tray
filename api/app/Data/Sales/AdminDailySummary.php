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

    public static function fromArray(array $d): self
    {
        return new self(
            date:            (string) ($d['date'] ?? ''),
            totalCount:      (int)    ($d['totalCount'] ?? 0),
            totalAmount:     (float)  ($d['totalAmount'] ?? 0),
            totalCommission: (float)  ($d['totalCommission'] ?? 0),
        );
    }

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
