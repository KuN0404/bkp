<?php

namespace App\Filament\Resources\CashProofOfExpenditureResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CashProofOfExpenditureResource;

class EditCashProofOfExpenditure extends EditRecord
{
    protected static string $resource = CashProofOfExpenditureResource::class;

    public function getRecord(): Model
    {
        // This is the key change: withTrashed()
        return $this->getResource()::getEloquentQuery()
            ->withTrashed() // This tells Eloquent to include soft-deleted records
            ->findOrFail($this->getRecordId());
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
