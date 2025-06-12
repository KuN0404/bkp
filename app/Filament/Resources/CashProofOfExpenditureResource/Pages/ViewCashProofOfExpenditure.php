<?php

namespace App\Filament\Resources\CashProofOfExpenditureResource\Pages;

use App\Filament\Resources\CashProofOfExpenditureResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCashProofOfExpenditure extends ViewRecord
{
    protected static string $resource = CashProofOfExpenditureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
