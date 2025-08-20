<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sellers\ResendCommissionRequest;
use App\Models\Seller;
use App\Services\Sales\DailySalesReportService;
use Illuminate\Support\Facades\Log;

class CommissionController extends Controller
{
    public function __construct(private DailySalesReportService $reports) {}

    // POST /api/v1/sellers/{seller}/commission/resend  { date: 'YYYY-MM-DD' }
    public function resendSellerDaily(ResendCommissionRequest $request, Seller $seller)
    {
        $date = $request->validated('date');
        $summary = $this->reports->sellerSummary($seller->id, $date);

        // Aqui faremos o envio por e-mail/queue (job) no próximo passo.
        // Ex.: dispatch(new SendSellerDailyCommissionMailJob($summary));
        Log::info('Commission resend requested', $summary->toArray());

        return response()->json([
            'message' => 'Reenvio solicitado. (E-mail será disparado pelo job de envio.)',
            'summary' => $summary->toArray(),
        ], 202);
    }
}
