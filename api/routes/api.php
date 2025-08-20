<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\SellerController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\CommissionController;

Route::prefix('v1')->group(function () {

    Route::apiResource('sellers', SellerController::class);

    Route::apiResource('sales', SaleController::class);

    Route::get('sellers/{seller}/sales', [SaleController::class, 'bySeller'])
        ->name('sellers.sales');

    Route::get('reports/daily/admin',   [ReportController::class, 'adminDaily'])
        ->name('reports.daily.admin');

    Route::get('reports/daily/sellers', [ReportController::class, 'sellersDaily'])
        ->name('reports.daily.sellers');

    Route::get('reports/daily/sellers/{seller}', [ReportController::class, 'sellerDaily'])
        ->name('reports.daily.seller');

    Route::post('sellers/{seller}/commission/resend', [CommissionController::class, 'resendSellerDaily'])
        ->name('sellers.commission.resend');
});
