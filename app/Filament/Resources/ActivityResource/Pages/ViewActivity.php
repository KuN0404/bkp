<?php

namespace App\Filament\Resources\ActivityResource\Pages;

use Filament\Actions;
use Filament\Actions\RestoreAction;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\ActivityResource;

class ViewActivity extends ViewRecord
{
    protected static string $resource = ActivityResource::class;

    /**
     * Override this method to customize how the record is resolved from the URL key.
     * Ini adalah cara yang direkomendasikan di Filament 3.
     *
     * @param int|string $key The ID from the URL.
     */
    public function resolveRecord(int|string $key): Model
    {
   // Ambil nama kelas model dari resource
    $modelClass = $this->getResource()::getModel();

    return $this->getResource()::getEloquentQuery()
        ->withTrashed()
        ->where((new $modelClass)->getQualifiedKeyName(), $key)
        ->firstOrFail();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            RestoreAction::make()
            ->label('Pulihkan')
            ->icon('heroicon-o-arrow-path')
            ->color('success')
            ->requiresConfirmation(),
        ];
    }
}
