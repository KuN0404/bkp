<?php

namespace App\Exports;

use App\Models\CashProofOfExpenditure;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CashProofOfExpendituresExport implements FromQuery, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function query()
    {
        // Eager load relasi untuk performa yang lebih baik
        return CashProofOfExpenditure::query()->with(['school', 'activity']);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // Mendefinisikan header kolom di file Excel
        return [
            'ID BKP',
            'Nama Sekolah',
            'Nama Kegiatan',
            'Jumlah Siswa',
            'Nominal',
            'Terbilang',
            'Total PPN', // Dari accessor
            'Total PPH', // Dari accessor
            'Total Pajak', // Dari accessor
        ];
    }

    /**
     * @var CashProofOfExpenditure $bkp
     */
    public function map($bkp): array
    {
        // Memetakan setiap record ke dalam format array yang diinginkan
        return [
            $bkp->id,
            $bkp->school->school_name,
            $bkp->activity->activity_name,
            $bkp->number_of_students,
            $bkp->nominal,
            $bkp->sorted,
            $bkp->total_ppn, // Memanggil accessor
            $bkp->total_pph, // Memanggil accessor
            $bkp->total_pajak, // Memanggil accessor
        ];
    }
}
