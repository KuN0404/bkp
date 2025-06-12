<?php

namespace App\Filament\Resources;

use App\Models\Activity;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\ActivityResource\Pages;
use App\Filament\Resources\ActivityResource\Pages\EditActivity;
use App\Filament\Resources\ActivityResource\Pages\CreateActivity;
use App\Filament\Resources\ActivityResource\Pages\ListActivities;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $modelLabel = 'Kegiatan';
    protected static ?string $navigationLabel = 'Data Kegiatan'; // <-- Mengubah nama di sidebar
    protected static ?string $pluralModelLabel = 'Daftar Kegiatan'; // <-- Judul di halaman utama resource


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('activity_name')
                    ->label('Nama Kegiatan')
                    ->required()
                    ->maxLength(255),
                TextInput::make('director_name')
                    ->label('Nama Penanggung Jawab')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('activity_name')->label('Nama Kegiatan')->searchable(),
                TextColumn::make('director_name')->label('Penanggung Jawab')->searchable(),
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
            'index' => Pages\ListActivities::route('/'),
            'create' => Pages\CreateActivity::route('/create'),
            'view' => Pages\ViewActivity::route('/{record}'),
            'edit' => Pages\EditActivity::route('/{record}/edit'),
        ];
    }
}
