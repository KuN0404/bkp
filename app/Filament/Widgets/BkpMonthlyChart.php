<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\CashProofOfExpenditure;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BkpMonthlyChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Pengeluaran per Bulan';
    protected static ?int $sort = 2; // Menampilkan di bawah Stats Overview

    protected function getData(): array
    {
        $currentYear = Carbon::now()->year;
        
        // Ambil data untuk tahun ini (hindari function MONTH() raw SQL karena SQLite tidak support)
        $records = CashProofOfExpenditure::select('created_at', 'nominal')
            ->whereYear('created_at', $currentYear)
            ->get();

        // Group by bulan menggunakan collection mapping (Aman di semua Database)
        $monthlyTotals = $records->groupBy(function ($item) {
            return $item->created_at->format('n'); // format 'n' mengembalikan angka bulan tanpa nol (1-12)
        })->map(function ($items) {
            return $items->sum('nominal');
        })->toArray();

        $data = [];
        $labels = [];
        
        foreach (range(1, 12) as $month) {
            $data[] = $monthlyTotals[$month] ?? 0;
            $labels[] = Carbon::create()->month($month)->format('M');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Nominal (Rp)',
                    'data' => $data,
                    'backgroundColor' => '#001F54', // Menggunakan warna primary
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
