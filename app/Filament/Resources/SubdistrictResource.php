<?php

namespace App\Filament\Resources;

use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Subdistrict;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\SubdistrictResource\Pages;

class SubdistrictResource extends Resource
{
    protected static ?string $model = Subdistrict::class;
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $modelLabel = 'Kecamatan';
    protected static ?string $navigationLabel = 'Data Kecamatan'; // <-- Mengubah nama di sidebar
    protected static ?string $pluralModelLabel = 'Daftar Kecamatan'; // <-- Judul di halaman utama resource


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('subdistrict_name')
                    ->label('Nama Kecamatan')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subdistrict_name')
                    ->label('Nama Kecamatan')
                    ->searchable(),
                TextColumn::make('deleted_at')
                    ->label('Status')
                    ->state(function (Subdistrict $record): string {
                        if ($record->deleted_at === null) {
                            return 'Aktif';
                        } else {
                            return 'Tidak Aktif';
                        }
                    })
                    ->color(function (Subdistrict $record): string {
                        if ($record->deleted_at === null) {
                            return 'success'; // Green for active records
                        } else {
                            return 'danger'; // Red for deleted records
                        }
                    })
                    ->icon(function (Subdistrict $record): ?string {
                        if ($record->deleted_at === null) {
                            return 'heroicon-o-check-circle'; // Icon for active records
                        } else {
                            return 'heroicon-o-x-circle'; // Icon for deleted records
                        }
                    })
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('l, d F Y - H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                ActionGroup::make([
                ViewAction::make()
                ->slideOver(),
                EditAction::make(),
                DeleteAction::make()
                    ->label('Hapus')
                    ->requiresConfirmation()
                    ,
                Action::make('restore')
                    ->label('Pulihkan')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Subdistrict $record) {
                        $record->restore();
                    })
                    ->hidden(fn (Subdistrict $record) => $record->deleted_at === null),
                    ])
                ->dropdown()
                ->label('Aksi')
                ->icon('heroicon-o-ellipsis-horizontal')
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubdistricts::route('/'),
            'create' => Pages\CreateSubdistrict::route('/create'),
            // 'view' => Pages\ViewSubdistrict::route('/{record}'),
            'edit' => Pages\EditSubdistrict::route('/{record}/edit'),
        ];
    }
}
