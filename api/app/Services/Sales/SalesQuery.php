<?php

namespace App\Services\Sales;

use App\Models\Sale;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SalesQuery
{

     // Se $sellerId for null, considera todos os vendedores.
    public function totalsForDate(string $date, ?int $sellerId = null): array
    {
        $query = Sale::query()->whereDate('sold_at', $date);

        if ($sellerId) {
            $query->where('seller_id', $sellerId);
        }

        $row = $query
            ->selectRaw('COUNT(*) AS c, COALESCE(SUM(amount),0) AS s')
            ->first();

        return [
            'count' => (int) ($row->c ?? 0),
            'sum'   => (float) ($row->s ?? 0.0),
        ];
    }

    // Apenas vendedores com pelo menos 1 venda no dia.
    public function groupBySellerForDate(string $date): Collection
    {
        return Sale::query()
            ->whereDate('sold_at', $date)
            ->select('seller_id', DB::raw('COUNT(*) AS c'), DB::raw('COALESCE(SUM(amount),0) AS s'))
            ->groupBy('seller_id')
            ->get();
    }
}
