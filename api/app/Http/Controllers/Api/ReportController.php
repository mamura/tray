<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Services\Sales\DailySalesReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private DailySalesReportService $reports) {}

    // GET /api/v1/reports/daily/admin?date=YYYY-MM-DD
    public function adminDaily(Request $request)
    {
        $date = $request->query('date') ?: now()->toDateString();
        $summary = $this->reports->adminSummary($date);

        return response()->json($summary->toArray());
    }

    // GET /api/v1/reports/daily/sellers?date=YYYY-MM-DD
    public function sellersDaily(Request $request)
    {
        $date = $request->query('date') ?: now()->toDateString();
        $items = $this->reports->allSellersSummaries($date)->map->toArray()->values();

        return response()->json([
            'date' => $date,
            'items' => $items,
        ]);
    }

    // GET /api/v1/reports/daily/sellers/{seller}?date=YYYY-MM-DD
    public function sellerDaily(Request $request, Seller $seller)
    {
        $date = $request->query('date') ?: now()->toDateString();
        $summary = $this->reports->sellerSummary($seller->id, $date);

        return response()->json($summary->toArray());
    }
}
