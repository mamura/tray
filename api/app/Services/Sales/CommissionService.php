<?php

namespace App\Services\Sales;

class CommissionService
{
    public function rate(): float
    {
        return (float) config('sales.commission_rate', 0.085);
    }

    public function forAmount(float $amount): float
    {
        return round($amount * $this->rate(), 2);
    }

    /**
     * Soma de comissões para um total agregado.
     * Obs.: isto arredonda no fim. Se for preciso arredontar por venda fazer isso no SQL.
     * ex.: SUM(ROUND(amount*rate,2))
     */
    public function forTotalAmount(float $totalAmount): float
    {
        return round($totalAmount * $this->rate(), 2);
    }
}
