<?php

namespace App\Services\Sales;

use App\Data\Sales\AdminDailySummary;
use App\Data\Sales\SellerDailySummary;
use App\Models\Seller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

class DailySalesReportService
{
    public function __construct(
        protected SalesQuery $query,
        protected CommissionService $commission
    ) {}

    /**
     * Resumo do dia para 1 vendedor.
     */
    public function sellerSummary(int $sellerId, string $date = null): SellerDailySummary
    {
        $date = $date ?: Date::now()->toDateString();

        $seller = Seller::findOrFail($sellerId);

        $totals = $this->query->totalsForDate($date, $sellerId);
        $totalCommission = $this->commission->forTotalAmount($totals['sum']);

        return new SellerDailySummary(
            sellerId: $seller->id,
            sellerName: $seller->name,
            sellerEmail: $seller->email,
            date: $date,
            count: $totals['count'],
            totalAmount: round($totals['sum'], 2),
            totalCommission: $totalCommission
        );
    }

    /**
     * Resumos de todos os vendedores com pelo menos 1 venda no dia.
     * (Se desejar incluir vendedores com 0 vendas, podemos trocar por left join em Sellers.)
     */
    public function allSellersSummaries(string $date = null): Collection
    {
        $date = $date ?: Date::now()->toDateString();

        $rows = $this->query->groupBySellerForDate($date);

        // Carrega os sellers de uma vez
        $sellers = Seller::whereIn('id', $rows->pluck('seller_id'))->get()->keyBy('id');

        return $rows->map(function ($r) use ($date, $sellers) {
            $seller = $sellers[$r->seller_id];
            $sum = (float) $r->s;
            return new SellerDailySummary(
                sellerId: $seller->id,
                sellerName: $seller->name,
                sellerEmail: $seller->email,
                date: $date,
                count: (int) $r->c,
                totalAmount: round($sum, 2),
                totalCommission: $this->commission->forTotalAmount($sum)
            );
        });
    }

    /**
     * Resumo total do dia para o admin.
     */
    public function adminSummary(string $date = null): AdminDailySummary
    {
        $date = $date ?: Date::now()->toDateString();

        $totals = $this->query->totalsForDate($date, null);
        $totalCommission = $this->commission->forTotalAmount($totals['sum']);

        return new AdminDailySummary(
            date: $date,
            totalCount: $totals['count'],
            totalAmount: round($totals['sum'], 2),
            totalCommission: $totalCommission
        );
    }

    public function allSellersSummariesIncludingZero(string $date = null): Collection
    {
        $date = $date ?: now()->toDateString();

        $rows = Seller::query()
            ->leftJoin('sales', function ($join) use ($date) {
                $join->on('sellers.id', '=', 'sales.seller_id')
                    ->whereDate('sales.sold_at', $date);
            })
            ->groupBy('sellers.id', 'sellers.name', 'sellers.email')
            ->selectRaw('sellers.id, sellers.name, sellers.email,
                        COUNT(sales.id) AS c, COALESCE(SUM(sales.amount),0) AS s')
            ->get();

        return $rows->map(function ($r) use ($date) {
            $sum = (float) $r->s;
            return new \App\Data\Sales\SellerDailySummary(
                sellerId: (int) $r->id,
                sellerName: (string) $r->name,
                sellerEmail: (string) $r->email,
                date: $date,
                count: (int) $r->c,
                totalAmount: round($sum, 2),
                totalCommission: app(\App\Services\Sales\CommissionService::class)->forTotalAmount($sum)
            );
        });
    }
}
