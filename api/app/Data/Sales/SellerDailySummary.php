<?php

namespace App\Data\Sales;

class SellerDailySummary
{
    public function __construct(
        public int $sellerId,
        public string $sellerName,
        public string $sellerEmail,
        public string $date,
        public int $count,
        public float $totalAmount,
        public float $totalCommission
    ) {}

    public function toArray(): array
    {
        return [
            'seller_id'       => $this->sellerId,
            'seller_name'     => $this->sellerName,
            'seller_email'    => $this->sellerEmail,
            'date'            => $this->date,
            'count'           => $this->count,
            'total_amount'    => $this->totalAmount,
            'total_commission'=> $this->totalCommission,
        ];
    }
}
