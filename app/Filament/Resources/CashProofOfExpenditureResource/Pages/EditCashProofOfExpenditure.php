<?php

namespace App\Filament\Resources\CashProofOfExpenditureResource\Pages;

use App\Filament\Resources\CashProofOfExpenditureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCashProofOfExpenditure extends EditRecord
{
    protected static string $resource = CashProofOfExpenditureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
