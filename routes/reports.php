<?php

use App\Http\Controllers\Reports\AddReportController;
use App\Http\Controllers\Reports\ShowReportController;
use Illuminate\Support\Facades\Route;

/// Free access
Route::get('/reports', [AddReportController::class, 'create']);

Route::get('/reports/report', [ShowReportController::class, 'create']);

/// Authorized'n'Verified
Route::middleware(['auth:sanctum', 'verified'])->group(function() {

    Route::post('/reports', [AddReportController::class, 'store']);

    Route::post('/reports/report', [ShowReportController::class, 'store']);
});