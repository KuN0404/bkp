<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BkpPrintController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/bkp/{record}/print', [BkpPrintController::class, 'print'])->name('bkp.print');
