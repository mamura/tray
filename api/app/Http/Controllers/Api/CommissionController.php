<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sellers\ResendCommissionRequest;
use App\Jobs\SendSellerDailyCommissionMailJob;
use App\Mail\SellerDailyCommissionMail;
use App\Models\Seller;
use App\Services\Sales\DailySalesReportService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CommissionController extends Controller
{
    public function __construct(private DailySalesReportService $reports) {}

    public function resendSellerDaily(ResendCommissionRequest $request, Seller $seller)
    {
        $date = $request->validated('date');
        $summary = $this->reports->sellerSummary($seller->id, $date);

        //Mail::to($summary->sellerEmail)->queue(new SellerDailyCommissionMail($summary));
        SendSellerDailyCommissionMailJob::dispatch($seller->id, $date)->onQueue('default');

        Log::info('Commission resend requested', $summary->toArray());

        return response()->json([
            'message' => 'Reenvio solicitado. (E-mail será disparado pelo job de envio.)',
            'summary' => $summary->toArray(),
        ], 202);
    }
}
