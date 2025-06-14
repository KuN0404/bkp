<?php

namespace App\Filament\Resources;

use App\Models\School;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\SchoolResource\Pages;
use App\Filament\Resources\SchoolResource\Pages\EditSchool;
use App\Filament\Resources\SchoolResource\Pages\ListSchools;
use App\Filament\Resources\SchoolResource\Pages\CreateSchool;

class SchoolResource extends Resource
{
    protected static ?string $model = School::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $navigationLabel = 'Data Sekolah'; // <-- Mengubah nama di sidebar

    protected static ?string $modelLabel = 'Sekolah'; // <-- Label untuk satu data
    protected static ?string $pluralModelLabel = 'Daftar Sekolah'; // <-- Judul di halaman utama resource


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('subdistrict_id') // Sesuaikan dengan nama kolom yang benar
                    ->relationship('subdistrict', 'subdistrict_name')
                    ->label('Kecamatan')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('school_name')->label('Nama Sekolah')->required(),
                TextInput::make('principal_name')->label('Nama Kepala Sekolah')->required(),
                TextInput::make('principal_nip')->label('NIP Kepala Sekolah')->nullable(),
                TextInput::make('treasurer_name')->label('Nama Bendahara')->required(),
                TextInput::make('treasurer_nip')->label('NIP Bendahara')->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('school_name')->label('Nama Sekolah')->searchable()->sortable(),
                TextColumn::make('subdistrict.subdistrict_name')->label('Kecamatan')->sortable(),
                TextColumn::make('principal_name')->label('Kepala Sekolah')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('treasurer_name')->label('Bendahara')->searchable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('subdistrict')->relationship('subdistrict', 'subdistrict_name'),
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
            'index' => Pages\ListSchools::route('/'),
            'create' => Pages\CreateSchool::route('/create'),
            'view' => Pages\ViewSchool::route('/{record}'),
            'edit' => Pages\EditSchool::route('/{record}/edit'),
        ];
    }
}
