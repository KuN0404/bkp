<?php

namespace App\Http\Controllers;

use App\Models\CashProofOfExpenditure;
use Illuminate\Http\Request;

class BkpPrintController extends Controller
{
    /**
     * Menampilkan halaman cetak untuk BKP tertentu.
     *
     * @param CashProofOfExpenditure $record
     * @return \Illuminate\View\View
     */
    public function print(CashProofOfExpenditure $record)
    {
        $record->load(['schoolWithTrashed.subdistrictWithTrashed', 'activityWithTrashed']);
        return view('print.bkp', ['bkp' => $record]);
    }
}
