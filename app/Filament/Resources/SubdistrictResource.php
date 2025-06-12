<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubdistrictResource\Pages;
use App\Models\Subdistrict;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;

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
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
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
            'view' => Pages\ViewSubdistrict::route('/{record}'),
            'edit' => Pages\EditSubdistrict::route('/{record}/edit'),
        ];
    }
}
