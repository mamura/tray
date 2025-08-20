<?php

use App\Jobs\DispatchSellerDailyCommissionsJob;
use App\Jobs\SendAdminDailySalesMailJob;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    logger()->info('scheduler: tick', ['ts' => now('America/Fortaleza')->toDateTimeString()]);
})->everyMinute()->name('debug:scheduler-tick');

// Enviar e-mails de commissão para vendedores (quem vendeu no dia)
Schedule::call(function () {
    $date = now('America/Fortaleza')->toDateString();
    DispatchSellerDailyCommissionsJob::dispatch($date)->onQueue('default');
    logger()->info('scheduler: dispatched seller emails', ['date' => $date]);
})
    ->everyMinute()
    //->dailyAt('23:55')
    ->name('daily:dispatch-seller-mails')
    ->withoutOverlapping();

// Enviar e-mail do admin (resumo geral + por vendedor)
Schedule::call(function () {
    $date = now('America/Fortaleza')->toDateString();
    SendAdminDailySalesMailJob::dispatch($date, true)->onQueue('default');
    logger()->info('scheduler: dispatched admin email', ['date' => $date]);
})
    ->everyMinute()
    //->dailyAt('23:58')
    ->name('daily:admin-mail')
    ->withoutOverlapping();
