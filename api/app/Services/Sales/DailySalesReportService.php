<?php

namespace App\Services\Sales;

use App\Data\Sales\AdminDailySummary;
use App\Data\Sales\SellerDailySummary;
use App\Models\Seller;
use App\Support\CacheKeys;
use App\Support\CacheTtl;
use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class DailySalesReportService
{
    public function __construct(
        protected SalesQuery $query,
        protected CommissionService $commission
    ) {}

    /**
     * Resumo do dia para 1 vendedor.
     */
    public function sellerSummary(int $sellerId, string $date): SellerDailySummary
    {
        $ttl = CacheTtl::forReportDate($date);

        if ($this->taggable()) {
            $data = Cache::tags(['reports', "date:{$date}", "seller:{$sellerId}"])
                ->remember("seller:{$sellerId}:{$date}", $ttl, function () use ($sellerId, $date) {
                    $totals = $this->query->totalsForDate($date, $sellerId);
                    $seller = Seller::select('name', 'email')->find($sellerId);

                    $sum        = (float) ($totals['sum'] ?? 0.0); 
                    $commission = $this->commission->forTotalAmount($sum);

                    return [
                        'sellerId'        => $sellerId,
                        'sellerName'      => (string) ($seller?->name ?? ''),
                        'sellerEmail'     => (string) ($seller?->email ?? ''),
                        'date'            => $date,
                        'count'           => (int)   ($totals['count'] ?? 0),
                        'totalAmount'     => $sum,
                        'totalCommission' => (float) $commission,
                    ];
                });
        } else {
            $key  = CacheKeys::sellerDaily($sellerId, $date);
            $data = Cache::remember($key, $ttl, function () use ($sellerId, $date) {
                $totals = $this->query->totalsForDate($date, $sellerId);
                $seller = Seller::select('name', 'email')->find($sellerId);

                $sum        = (float) ($totals['sum'] ?? 0.0);   
                $commission = $this->commission->forTotalAmount($sum);

                return [
                    'sellerId'        => $sellerId,
                    'sellerName'      => (string) ($seller?->name ?? ''),
                    'sellerEmail'     => (string) ($seller?->email ?? ''),
                    'date'            => $date,
                    'count'           => (int)   ($totals['count'] ?? 0),
                    'totalAmount'     => $sum,
                    'totalCommission' => (float) $commission,
                ];
            });
        }

        return SellerDailySummary::fromArray($data);
    }

    /**
     * Resumos de todos os vendedores com pelo menos 1 venda no dia.
     */
    public function allSellersSummaries(string $date): Collection
    {
        $ttl = CacheTtl::forReportDate($date);

        if ($this->taggable()) {
            $arr = Cache::tags(['reports', "date:{$date}"])
                ->remember("sellers:list:{$date}", $ttl, function () use ($date) {
                    return $this->query->groupBySellerForDate($date)->map(function ($row) use ($date) {
                        $sum        = (float) ($row->sum ?? 0.0);  
                        $commission = $this->commission->forTotalAmount($sum);

                        return [
                            'sellerId'        => (int) $row->seller_id,
                            'sellerName'      => (string) $row->seller_name,
                            'sellerEmail'     => (string) $row->seller_email,
                            'date'            => $date,
                            'count'           => (int)   ($row->count ?? 0),
                            'totalAmount'     => $sum,
                            'totalCommission' => (float) $commission,
                        ];
                    })->values()->all();
                });
        } else {
            $key = CacheKeys::sellersDailyList($date);
            $arr = Cache::remember($key, $ttl, function () use ($date) {
                return $this->query->groupBySellerForDate($date)->map(function ($row) use ($date) {
                    $sum        = (float) ($row->sum ?? 0.0);      
                    $commission = $this->commission->forTotalAmount($sum);

                    return [
                        'sellerId'        => (int) $row->seller_id,
                        'sellerName'      => (string) $row->seller_name,
                        'sellerEmail'     => (string) $row->seller_email,
                        'date'            => $date,
                        'count'           => (int)   ($row->count ?? 0),
                        'totalAmount'     => $sum,
                        'totalCommission' => (float) $commission,
                    ];
                })->values()->all();
            });
        }

        return collect($arr)->map(fn ($d) => SellerDailySummary::fromArray($d));
    }

    /**
     * Resumo total do dia para o admin.
     */
    public function adminSummary(string $date): AdminDailySummary
    {
        $ttl = CacheTtl::forReportDate($date);

        if ($this->taggable()) {
            $data = Cache::tags(['reports', "date:{$date}"])
                ->remember("admin:{$date}", $ttl, function () use ($date) {
                    $totals = $this->query->totalsForDate($date);

                    $sum        = (float) ($totals['sum'] ?? 0.0);  
                    $commission = $this->commission->forTotalAmount($sum);

                    return [
                        'date'            => $date,
                        'totalCount'      => (int)   ($totals['count'] ?? 0),
                        'totalAmount'     => $sum,
                        'totalCommission' => (float) $commission,
                    ];
                });
        } else {
            $key  = CacheKeys::adminDaily($date);
            $data = Cache::remember($key, $ttl, function () use ($date) {
                $totals = $this->query->totalsForDate($date);

                $sum        = (float) ($totals['sum'] ?? 0.0);      
                $commission = $this->commission->forTotalAmount($sum);

                return [
                    'date'            => $date,
                    'totalCount'      => (int)   ($totals['count'] ?? 0),
                    'totalAmount'     => $sum,
                    'totalCommission' => (float) $commission,
                ];
            });
        }

        return AdminDailySummary::fromArray($data);
    }

    /**
     * Resumos de todos os vendedores, incluindo quem não vendeu (zero).
     */
    public function allSellersSummariesIncludingZero(?string $date): Collection
    {
        $tz   = config('app.timezone', 'UTC');
        $date = $date ?: now($tz)->toDateString();
        $ttl  = CacheTtl::forReportDate($date);

        $build = function () use ($date) {
            // LEFT JOIN para incluir vendedores sem vendas
            $rows = Seller::query()
                ->leftJoin('sales', function ($join) use ($date) {
                    $join->on('sellers.id', '=', 'sales.seller_id')
                         ->whereDate('sales.sold_at', $date);
                })
                ->groupBy('sellers.id', 'sellers.name', 'sellers.email')
                ->selectRaw('sellers.id, sellers.name, sellers.email,
                             COUNT(sales.id) AS c,
                             COALESCE(SUM(sales.amount), 0) AS s')
                ->get();

            // serializa para cache (array simples)
            return $rows->map(function ($r) use ($date) {
                $sum        = (float) $r->s;                      
                $commission = $this->commission->forTotalAmount($sum);

                return [
                    'sellerId'        => (int) $r->id,
                    'sellerName'      => (string) $r->name,
                    'sellerEmail'     => (string) $r->email,
                    'date'            => $date,
                    'count'           => (int) $r->c,
                    'totalAmount'     => round($sum, 2),
                    'totalCommission' => (float) $commission,
                ];
            })->values()->all();
        };

        if ($this->taggable()) {
            $arr = Cache::tags(['reports', "date:{$date}"])
                ->remember("sellers:list:{$date}:includingZero", $ttl, $build);
        } else {
            $key = CacheKeys::sellersDailyListIncludingZero($date);
            $arr = Cache::remember($key, $ttl, $build);
        }

        return collect($arr)->map(fn ($d) => SellerDailySummary::fromArray($d));
    }

    /**
     * O store atual suporta tags (Redis/Memcached/Array)?
     */
    private function taggable(): bool
    {
        return Cache::getStore() instanceof TaggableStore;
    }
}
