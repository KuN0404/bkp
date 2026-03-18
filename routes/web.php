<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BkpPrintController;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/bkp/{record}/print', [BkpPrintController::class, 'print'])->name('bkp.print');
});
