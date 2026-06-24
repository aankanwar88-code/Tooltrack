<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Kirim pengingat kalibrasi setiap tanggal 1 pukul 07.00
Schedule::command('kalibrasi:kirim-pengingat')
    ->monthlyOn(1, '07:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error(
            'Scheduler kalibrasi:kirim-pengingat gagal dijalankan.'
        );
    });
