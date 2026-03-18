<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\CashProofOfExpenditure;
use App\Models\School;
use Carbon\Carbon;

class BkpStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Menggunakan query builder langsung untuk performa (aggregates tidak memicu N+1)
        $totalBkpBulanIni = CashProofOfExpenditure::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->count();

        $totalNominalBulanIni = CashProofOfExpenditure::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->sum('nominal');

        $totalSekolah = School::count();

        return [
            Stat::make('Total BKP (Bulan Ini)', $totalBkpBulanIni)
                ->description('Jumlah BKP yang dibuat bulan ini')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
            
            Stat::make('Total Nominal (Bulan Ini)', 'Rp ' . number_format($totalNominalBulanIni, 0, ',', '.'))
                ->description('Total uang yang dikeluarkan')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            
            Stat::make('Total Sekolah', $totalSekolah)
                ->description('Total sekolah terdaftar')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),
        ];
    }
}
