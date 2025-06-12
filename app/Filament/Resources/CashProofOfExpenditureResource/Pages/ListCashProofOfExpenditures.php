<?php

namespace App\Filament\Resources\CashProofOfExpenditureResource\Pages;

use App\Filament\Resources\CashProofOfExpenditureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCashProofOfExpenditures extends ListRecords
{
    protected static string $resource = CashProofOfExpenditureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
